#!/usr/bin/env python3
"""Membuat foto placeholder untuk data contoh Yumnia.

Semua gambar diberi label "FOTO CONTOH" supaya jelas bukan foto asli.
Jalankan ulang kapan saja:  python3 tools/generate-placeholders.py
Butuh: pip install pillow numpy
"""
import json
import math
import os
import random
import sys

import numpy as np
from PIL import Image, ImageDraw, ImageFilter, ImageFont

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
OUT = os.path.join(ROOT, "contoh-data", "gambar")
FONT = os.path.join(os.path.dirname(os.path.abspath(__file__)), "jost.ttf")
SS = 2  # supersampling

CREAM = "#FBF7F2"
BLUSH = "#F3DAD7"
BLUSH_SOFT = "#F8E9E6"
MAROON = "#7B1E3A"
SKIN = "#E3BFA3"


# ---------------------------------------------------------------- warna
def rgb(h):
    h = h.lstrip("#")
    return tuple(int(h[i:i + 2], 16) for i in (0, 2, 4))


def mix(a, b, t):
    a, b = rgb(a) if isinstance(a, str) else a, rgb(b) if isinstance(b, str) else b
    return tuple(int(round(a[i] + (b[i] - a[i]) * t)) for i in range(3))


def hexs(c):
    return "#%02x%02x%02x" % c


def shade(h, t):
    """t>0 lebih terang, t<0 lebih gelap."""
    return hexs(mix(h, "#ffffff", t)) if t > 0 else hexs(mix(h, "#140308", -t))


# ---------------------------------------------------------------- dasar
def gradient(w, h, top, bottom, light=True):
    y = np.linspace(0, 1, h)[:, None]
    x = np.linspace(0, 1, w)[None, :]
    a, b = np.array(rgb(top), float), np.array(rgb(bottom), float)
    g = a[None, None, :] + (b - a)[None, None, :] * y[..., None]
    g = np.broadcast_to(g, (h, w, 3)).copy()
    if light:  # cahaya lembut dari kiri atas
        d = np.sqrt((x - 0.25) ** 2 + (y - 0.15) ** 2)
        g *= (1.06 - 0.12 * np.clip(d, 0, 1.2))[..., None]
    return Image.fromarray(np.clip(g, 0, 255).astype(np.uint8), "RGB").convert("RGBA")


def fold_texture(w, h, seed=1, strength=1.0, freq=3.0, angle=0.0):
    rnd = random.Random(seed)
    y = np.linspace(0, 1, h)[:, None]
    x = np.linspace(0, 1, w)[None, :]
    ca, sa = math.cos(angle), math.sin(angle)
    u = x * ca + y * sa
    v = -x * sa + y * ca
    p1, p2, p3 = rnd.random() * 6, rnd.random() * 6, rnd.random() * 6
    f = (0.10 * np.sin(2 * np.pi * (u * freq + 0.18 * np.sin(2 * np.pi * v * 1.2 + p1)) + p2)
         + 0.05 * np.sin(2 * np.pi * (u * freq * 2.3 + v * 0.5) + p3)
         + 0.03 * np.sin(2 * np.pi * (u * freq * 5.1 - v * 0.8)))
    f += 0.10 * (0.45 - x) - 0.06 * y
    return 1 + f * strength


def fill_layer(size, color, mask, texture=None):
    w, h = size
    base = np.zeros((h, w, 3), float) + np.array(rgb(color), float)
    if texture is not None:
        base *= texture[..., None]
    layer = Image.fromarray(np.clip(base, 0, 255).astype(np.uint8), "RGB").convert("RGBA")
    layer.putalpha(mask)
    return layer


def shadow(img, mask, offset=(0, 0), blur=20, opacity=0.25, color=(60, 20, 30)):
    sh = Image.new("RGBA", img.size, color + (0,))
    m = mask.point(lambda v: int(v * opacity))
    m = m.filter(ImageFilter.GaussianBlur(blur))
    canvas = Image.new("L", img.size, 0)
    canvas.paste(m, offset)
    sh.putalpha(canvas)
    img.alpha_composite(sh)


def new_mask(size):
    return Image.new("L", size, 0)


def bezier(p0, p1, p2, p3, n=24):
    pts = []
    for i in range(n + 1):
        t = i / n
        mt = 1 - t
        pts.append((mt ** 3 * p0[0] + 3 * mt ** 2 * t * p1[0] + 3 * mt * t ** 2 * p2[0] + t ** 3 * p3[0],
                    mt ** 3 * p0[1] + 3 * mt ** 2 * t * p1[1] + 3 * mt * t ** 2 * p2[1] + t ** 3 * p3[1]))
    return pts


def catmull(points, n=12, closed=True):
    pts = []
    m = len(points)
    rng = range(m) if closed else range(m - 1)
    for i in rng:
        p0, p1, p2, p3 = (points[(i - 1) % m], points[i], points[(i + 1) % m], points[(i + 2) % m])
        for k in range(n):
            t = k / n
            t2, t3 = t * t, t * t * t
            pts.append(tuple(0.5 * ((2 * p1[j]) + (-p0[j] + p2[j]) * t
                                    + (2 * p0[j] - 5 * p1[j] + 4 * p2[j] - p3[j]) * t2
                                    + (-p0[j] + 3 * p1[j] - 3 * p2[j] + p3[j]) * t3) for j in range(2)))
    return pts


def arch(draw, x0, y0, x1, y1, fill):
    r = (x1 - x0) / 2
    draw.ellipse([x0, y0, x1, y0 + 2 * r], fill=fill)
    draw.rectangle([x0, y0 + r, x1, y1], fill=fill)


# ---------------------------------------------------------------- figur berhijab
def draw_figure(img, cx, cy, u, hijab, garment=CREAM, skin=SKIN, seed=1, long_drape=False):
    """cx, cy = pusat kepala; u = satuan ukuran (kira-kira radius kepala)."""
    W, H = img.size
    # badan / pakaian (bahu lebar)
    m = new_mask(img.size)
    d = ImageDraw.Draw(m)
    d.ellipse([cx - 3.5 * u, cy + 2.1 * u, cx + 3.5 * u, cy + 8.6 * u], fill=255)
    d.rectangle([cx - 3.5 * u, min(cy + 5.4 * u, H), cx + 3.5 * u, H], fill=255)
    tex = fold_texture(W, H, seed + 7, 0.6, 2.2, 0.15)
    shadow(img, m, (int(0.25 * u), int(0.2 * u)), int(0.6 * u), 0.18)
    img.alpha_composite(fill_layer(img.size, garment, m, tex))

    # kerudung (drape)
    bottom = cy + (6.3 if long_drape else 4.7) * u
    path = []
    path += bezier((cx, cy - 1.45 * u), (cx + 0.95 * u, cy - 1.48 * u), (cx + 1.38 * u, cy - 0.8 * u), (cx + 1.36 * u, cy + 0.15 * u))
    path += bezier((cx + 1.36 * u, cy + 0.15 * u), (cx + 1.36 * u, cy + 1.0 * u), (cx + 1.5 * u, cy + 1.6 * u), (cx + 2.35 * u, cy + 2.45 * u))
    path += bezier((cx + 2.35 * u, cy + 2.45 * u), (cx + 2.75 * u, cy + 2.9 * u), (cx + 2.5 * u, bottom - 0.9 * u), (cx + 1.9 * u, bottom - 0.45 * u))
    path += bezier((cx + 1.9 * u, bottom - 0.45 * u), (cx + 1.1 * u, bottom + 0.05 * u), (cx + 0.4 * u, bottom + 0.1 * u), (cx, bottom + 0.15 * u))
    path += bezier((cx, bottom + 0.15 * u), (cx - 0.4 * u, bottom + 0.1 * u), (cx - 1.1 * u, bottom + 0.05 * u), (cx - 1.9 * u, bottom - 0.45 * u))
    path += bezier((cx - 1.9 * u, bottom - 0.45 * u), (cx - 2.5 * u, bottom - 0.9 * u), (cx - 2.75 * u, cy + 2.9 * u), (cx - 2.35 * u, cy + 2.45 * u))
    path += bezier((cx - 2.35 * u, cy + 2.45 * u), (cx - 1.5 * u, cy + 1.6 * u), (cx - 1.36 * u, cy + 1.0 * u), (cx - 1.36 * u, cy + 0.15 * u))
    path += bezier((cx - 1.36 * u, cy + 0.15 * u), (cx - 1.38 * u, cy - 0.8 * u), (cx - 0.95 * u, cy - 1.48 * u), (cx, cy - 1.45 * u))
    m = new_mask(img.size)
    ImageDraw.Draw(m).polygon(path, fill=255)
    shadow(img, m, (int(0.3 * u), int(0.35 * u)), int(0.7 * u), 0.28)
    tex = fold_texture(W, H, seed, 1.1, 3.0, -0.08)
    img.alpha_composite(fill_layer(img.size, hijab, m, tex))

    # lipatan kain
    fl = Image.new("RGBA", img.size, (0, 0, 0, 0))
    fd = ImageDraw.Draw(fl)
    dark = rgb(shade(hijab, -0.35)) + (110,)
    light = rgb(shade(hijab, 0.25)) + (90,)
    for sx, ex, c in ((-0.6, -1.1, dark), (0.65, 1.0, dark), (-1.2, -1.8, light), (1.3, 1.7, dark), (0.05, -0.15, light)):
        line = bezier((cx + sx * u, cy + 1.35 * u), (cx + (sx * 1.2) * u, cy + 2.3 * u),
                      (cx + ex * u, cy + 3.2 * u), (cx + ex * 0.9 * u, bottom - 0.35 * u), 30)
        fd.line(line, fill=c, width=max(2, int(0.07 * u)), joint="curve")
    fl = fl.filter(ImageFilter.GaussianBlur(0.09 * u))
    clip = Image.new("L", img.size, 0)
    clip.paste(fl.getchannel("A"), (0, 0), m)
    fl.putalpha(clip)
    img.alpha_composite(fl)

    # bingkai wajah + wajah
    rim = new_mask(img.size)
    ImageDraw.Draw(rim).ellipse([cx - 0.82 * u, cy - 0.86 * u, cx + 0.82 * u, cy + 1.22 * u], fill=255)
    img.alpha_composite(fill_layer(img.size, shade(hijab, -0.3), rim.filter(ImageFilter.GaussianBlur(0.05 * u))))
    face = new_mask(img.size)
    ImageDraw.Draw(face).ellipse([cx - 0.7 * u, cy - 0.72 * u, cx + 0.7 * u, cy + 1.1 * u], fill=255)
    ftex = fold_texture(W, H, seed + 3, 0.25, 0.8, 0.0)
    img.alpha_composite(fill_layer(img.size, skin, face.filter(ImageFilter.GaussianBlur(0.03 * u)), ftex))
    # bayangan dagu ke kain
    ch = new_mask(img.size)
    ImageDraw.Draw(ch).ellipse([cx - 0.8 * u, cy + 1.05 * u, cx + 0.8 * u, cy + 1.65 * u], fill=255)
    chl = Image.new("RGBA", img.size, rgb(shade(hijab, -0.55)) + (0,))
    a = ch.filter(ImageFilter.GaussianBlur(0.22 * u)).point(lambda v: int(v * 0.45))
    clip = Image.new("L", img.size, 0)
    clip.paste(a, (0, 0), m)
    chl.putalpha(clip)
    img.alpha_composite(chl)


# ---------------------------------------------------------------- sandal
FOOT = [(0.50, 1.00), (0.78, 0.97), (0.90, 0.85), (0.88, 0.66), (0.90, 0.47), (0.96, 0.31), (0.94, 0.15),
        (0.78, 0.04), (0.52, 0.00), (0.28, 0.03), (0.10, 0.14), (0.05, 0.30), (0.11, 0.47), (0.21, 0.62),
        (0.16, 0.80), (0.24, 0.95)]


def foot_path(x, y, w, h, mirror=False, rot=0.0, inset=0.0):
    pts = catmull(FOOT, 10)
    out = []
    ccx, ccy = 0.5, 0.5
    for px, py in pts:
        px = ccx + (px - ccx) * (1 - inset)
        py = ccy + (py - ccy) * (1 - inset * 0.6)
        if mirror:
            px = 1 - px
        X, Y = (px - 0.5) * w, (py - 0.5) * h
        c, s = math.cos(rot), math.sin(rot)
        out.append((x + X * c - Y * s, y + X * s + Y * c))
    return out


def local(x, y, w, h, rot, px, py, mirror=False):
    if mirror:
        px = 1 - px
    X, Y = (px - 0.5) * w, (py - 0.5) * h
    c, s = math.cos(rot), math.sin(rot)
    return (x + X * c - Y * s, y + X * s + Y * c)


def draw_sandal(img, x, y, w, h, color, model, mirror=False, rot=0.0, sole="#C9A98C", seed=2):
    W, H = img.size
    m = new_mask(img.size)
    ImageDraw.Draw(m).polygon(foot_path(x, y, w, h, mirror, rot), fill=255)
    shadow(img, m, (int(0.04 * w), int(0.03 * h)), int(0.06 * w), 0.32)
    img.alpha_composite(fill_layer(img.size, shade(sole, -0.18), m))
    ins = new_mask(img.size)
    ImageDraw.Draw(ins).polygon(foot_path(x, y, w, h, mirror, rot, inset=0.07), fill=255)
    img.alpha_composite(fill_layer(img.size, shade(sole, 0.35), ins, fold_texture(W, H, seed, 0.35, 1.0)))

    st = new_mask(img.size)
    sd = ImageDraw.Draw(st)
    L = lambda px, py: local(x, y, w, h, rot, px, py, mirror)
    bw = int(0.17 * w)
    if model == "silang":
        sd.line([L(-0.02, 0.24), L(1.02, 0.52)], fill=255, width=bw)
        sd.line([L(1.02, 0.24), L(-0.02, 0.52)], fill=255, width=bw)
    elif model == "anyam":
        sd.polygon([L(-0.03, 0.20), L(1.03, 0.18), L(1.03, 0.50), L(-0.03, 0.52)], fill=255)
    elif model == "balok":
        sd.polygon([L(-0.03, 0.20), L(1.03, 0.20), L(1.03, 0.38), L(-0.03, 0.38)], fill=255)
        sd.line([L(-0.04, 0.80), L(1.04, 0.80)], fill=255, width=int(0.07 * w))
    elif model == "pita":
        mm = new_mask(img.size)
        ImageDraw.Draw(mm).polygon(foot_path(x, y, w, h, mirror, rot, inset=-0.02), fill=255)
        box = new_mask(img.size)
        ImageDraw.Draw(box).polygon([L(-0.2, -0.2), L(1.2, -0.2), L(1.2, 0.44), L(-0.2, 0.44)], fill=255)
        st.paste(255, (0, 0), Image.fromarray(np.minimum(np.array(mm), np.array(box))))
    elif model == "wedges":
        sd.polygon([L(-0.03, 0.20), L(1.03, 0.20), L(1.03, 0.31), L(-0.03, 0.31)], fill=255)
        sd.polygon([L(-0.03, 0.40), L(1.03, 0.40), L(1.03, 0.51), L(-0.03, 0.51)], fill=255)
    st = st.filter(ImageFilter.GaussianBlur(0.004 * w))
    shadow(img, st, (int(0.02 * w), int(0.025 * h)), int(0.03 * w), 0.4)
    img.alpha_composite(fill_layer(img.size, color, st, fold_texture(W, H, seed + 5, 0.8, 1.6, 0.6)))
    if model == "anyam":
        wv = Image.new("RGBA", img.size, (0, 0, 0, 0))
        wd = ImageDraw.Draw(wv)
        c = rgb(shade(color, -0.3)) + (120,)
        for i in range(-6, 14):
            t = i / 10
            wd.line([L(t, 0.18), L(t + 0.3, 0.52)], fill=c, width=max(2, int(0.012 * w)))
        clip = Image.new("L", img.size, 0)
        clip.paste(wv.getchannel("A"), (0, 0), st)
        wv.putalpha(clip)
        img.alpha_composite(wv)
    if model == "pita":
        bd = ImageDraw.Draw(img)
        cxp, cyp = L(0.5, 0.36)
        r = 0.09 * w
        dc = shade(color, -0.25)
        bd.ellipse([cxp - 2.1 * r, cyp - r, cxp - 0.1 * r, cyp + r], fill=dc)
        bd.ellipse([cxp + 0.1 * r, cyp - r, cxp + 2.1 * r, cyp + r], fill=dc)
        bd.ellipse([cxp - 0.55 * r, cyp - 0.6 * r, cxp + 0.55 * r, cyp + 0.6 * r], fill=shade(color, -0.45))
    if model == "balok":
        bd = ImageDraw.Draw(img)
        bx, by = L(0.9, 0.8)
        bd.ellipse([bx - 0.03 * w, by - 0.03 * w, bx + 0.03 * w, by + 0.03 * w], fill="#D8C08A")


def draw_pair(img, cx, cy, size, color, model, seed=2):
    w, h = size * 0.42, size
    draw_sandal(img, cx - 0.26 * size, cy + 0.02 * size, w, h, color, model, mirror=True, rot=math.radians(-7), seed=seed)
    draw_sandal(img, cx + 0.22 * size, cy - 0.04 * size, w, h, color, model, mirror=False, rot=math.radians(6), seed=seed + 1)


def draw_side(img, x, y, length, color, model, sole="#C9A98C"):
    """Tampak samping, x,y = ujung belakang di lantai."""
    d = ImageDraw.Draw(img)
    heel = {"balok": 0.20, "wedges": 0.16, "anyam": 0.05, "silang": 0.04, "pita": 0.03}[model] * length
    thick = 0.035 * length
    floor = Image.new("L", img.size, 0)
    ImageDraw.Draw(floor).ellipse([x - 0.05 * length, y - 0.02 * length, x + 1.05 * length, y + 0.04 * length], fill=255)
    shadow(img, floor, (0, 0), int(0.03 * length), 0.35)
    top = []
    top += bezier((x, y - heel - thick), (x + 0.35 * length, y - heel - thick), (x + 0.55 * length, y - thick * 1.2), (x + length, y - thick))
    sole_poly = top + [(x + length, y), (x + 0.6 * length, y)]
    if model == "balok":
        sole_poly += [(x + 0.62 * length, y), (x + 0.6 * length, y - thick), (x + 0.2 * length, y - heel * 0.85), (x + 0.2 * length, y), (x, y)]
    elif model == "wedges":
        sole_poly += [(x, y)]
    else:
        sole_poly += [(x, y)]
    d.polygon(sole_poly, fill=shade(sole, -0.15))
    d.line(top, fill=shade(sole, 0.3), width=max(2, int(0.01 * length)))
    sc = rgb(color)
    if model in ("silang", "anyam", "wedges", "balok"):
        d.arc([x + 0.56 * length, y - thick - 0.15 * length, x + 0.94 * length, y - thick + 0.12 * length], 185, 355, fill=sc, width=int(0.05 * length))
    if model == "balok":
        d.arc([x - 0.02 * length, y - heel - 0.2 * length, x + 0.24 * length, y - heel + 0.02 * length], 200, 340, fill=sc, width=int(0.02 * length))
    if model == "wedges":
        d.arc([x + 0.36 * length, y - heel * 0.6 - 0.14 * length, x + 0.62 * length, y - heel * 0.6 + 0.08 * length], 185, 355, fill=sc, width=int(0.045 * length))
    if model == "pita":
        d.chord([x + 0.55 * length, y - thick - 0.1 * length, x + 1.02 * length, y - thick + 0.06 * length], 180, 360, fill=sc)
        bx, by = x + 0.72 * length, y - thick - 0.09 * length
        r = 0.035 * length
        d.ellipse([bx - 2 * r, by - r, bx, by + r], fill=shade(color, -0.25))
        d.ellipse([bx, by - r, bx + 2 * r, by + r], fill=shade(color, -0.25))


# ---------------------------------------------------------------- label & simpan
def label(img, text="FOTO CONTOH"):
    W, H = img.size
    size = max(18, int(min(W, H) * 0.026))
    try:
        f = ImageFont.truetype(FONT, size)
        try:
            f.set_variation_by_axes([500])
        except Exception:
            pass
    except Exception:
        f = ImageFont.load_default()
    spaced = " ".join(text)
    d = ImageDraw.Draw(img)
    tw = d.textlength(spaced, font=f)
    pad = size * 0.8
    x0, y0 = int(size * 1.4), int(H - size * 1.4 - size - pad)
    box = Image.new("RGBA", img.size, (0, 0, 0, 0))
    ImageDraw.Draw(box).rounded_rectangle([x0, y0, x0 + tw + 2 * pad, y0 + size + pad], radius=int((size + pad) / 2), fill=(255, 255, 255, 190))
    img.alpha_composite(box)
    d.text((x0 + pad, y0 + pad / 2 - size * 0.12), spaced, font=f, fill=rgb(MAROON))


def grain(img, amount=4, seed=0):
    a = np.array(img.convert("RGB")).astype(np.int16)
    rs = np.random.RandomState(seed)
    n = rs.randint(-amount, amount + 1, a.shape[:2])[..., None]
    return Image.fromarray(np.clip(a + n, 0, 255).astype(np.uint8))


def save(img, name, w, h, text="FOTO CONTOH", seed=0):
    img = img.resize((w, h), Image.LANCZOS)
    img = img.convert("RGBA")
    label(img, text)
    img = grain(img, 3, seed)
    path = os.path.join(OUT, name)
    img.save(path, "JPEG", quality=80, optimize=True, progressive=True)
    print("  ", name, os.path.getsize(path) // 1024, "KB")


def canvas(w, h, top, bottom):
    return gradient(w * SS, h * SS, top, bottom)


def backdrop_for(hexcol):
    top = hexs(mix(CREAM, hexcol, 0.10))
    bottom = hexs(mix(BLUSH_SOFT, hexcol, 0.22))
    return top, bottom


# ---------------------------------------------------------------- komposisi
def worn(name, color, seed, w=1000, h=1250, garment=CREAM, long_drape=False, text="FOTO CONTOH"):
    top, bottom = backdrop_for(color)
    img = canvas(w, h, top, bottom)
    W, H = img.size
    d = ImageDraw.Draw(img)
    arch(d, W * 0.16, H * 0.08, W * 0.84, H, hexs(mix(top, bottom, 0.9)) + "")
    u = W * 0.105
    draw_figure(img, W * 0.5, H * 0.36, u, color, garment, seed=seed, long_drape=long_drape)
    save(img, name, w, h, text, seed)


def flat_square(name, color, seed, pattern=None, w=1000, h=1250):
    top, bottom = backdrop_for(color)
    img = canvas(w, h, BLUSH_SOFT, hexs(mix(BLUSH, color, 0.12)))
    W, H = img.size
    # kain terlipat
    layer = Image.new("RGBA", img.size, (0, 0, 0, 0))
    m = new_mask(img.size)
    rw, rh = W * 0.62, W * 0.5
    ImageDraw.Draw(m).rounded_rectangle([W / 2 - rw / 2, H / 2 - rh / 2, W / 2 + rw / 2, H / 2 + rh / 2], radius=int(W * 0.02), fill=255)
    m = m.rotate(-7, resample=Image.BICUBIC, center=(W / 2, H / 2))
    shadow(img, m, (int(W * 0.015), int(W * 0.03)), int(W * 0.035), 0.32)
    tex = fold_texture(W, H, seed, 0.8, 2.0, 0.9)
    img.alpha_composite(fill_layer(img.size, color, m, tex))
    # lipatan diagonal
    fm = new_mask(img.size)
    ImageDraw.Draw(fm).polygon([(W / 2 - rw / 2, H / 2 - rh / 2), (W / 2 + rw * 0.1, H / 2 - rh / 2), (W / 2 - rw / 2, H / 2 + rh * 0.25)], fill=255)
    fm = fm.rotate(-7, resample=Image.BICUBIC, center=(W / 2, H / 2))
    shadow(img, fm, (int(W * 0.006), int(W * 0.01)), int(W * 0.012), 0.35)
    img.alpha_composite(fill_layer(img.size, shade(color, 0.12), fm, fold_texture(W, H, seed + 2, 0.6, 1.4, 0.3)))
    if pattern == "monogram":
        pat = Image.new("RGBA", img.size, (0, 0, 0, 0))
        pd = ImageDraw.Draw(pat)
        try:
            f = ImageFont.truetype(FONT, int(W * 0.045))
        except Exception:
            f = ImageFont.load_default()
        c = rgb(shade(color, -0.25)) + (95,)
        for yy in range(0, H, int(W * 0.08)):
            for xx in range(0, W, int(W * 0.08)):
                off = int(W * 0.04) if (yy // int(W * 0.08)) % 2 else 0
                pd.text((xx + off, yy), "Y", font=f, fill=c)
        clip = Image.new("L", img.size, 0)
        clip.paste(pat.getchannel("A"), (0, 0), m)
        pat.putalpha(clip)
        img.alpha_composite(pat)
    if pattern == "plisket":
        pat = Image.new("RGBA", img.size, (0, 0, 0, 0))
        pd = ImageDraw.Draw(pat)
        c = rgb(shade(color, -0.22)) + (120,)
        step = max(4, int(W * 0.012))
        for xx in range(0, W, step):
            pd.line([(xx, 0), (xx, H)], fill=c, width=max(1, step // 3))
        pat = pat.rotate(-7, resample=Image.BICUBIC, center=(W / 2, H / 2))
        clip = Image.new("L", img.size, 0)
        clip.paste(pat.getchannel("A"), (0, 0), m)
        pat.putalpha(clip)
        img.alpha_composite(pat)
    save(img, name, w, h, seed=seed)


def flat_hood(name, color, seed, w=1000, h=1250):
    img = canvas(w, h, BLUSH_SOFT, hexs(mix(BLUSH, color, 0.12)))
    W, H = img.size
    tmp = Image.new("RGBA", img.size, (0, 0, 0, 0))
    draw_figure(tmp, W * 0.5, H * 0.3, W * 0.11, color, garment=color, skin=hexs(mix(BLUSH_SOFT, color, 0.35)), seed=seed)
    # buang badan: pakai hanya bagian atas
    a = np.array(tmp)
    a[int(H * 0.84):, :, 3] = 0
    tmp = Image.fromarray(a).rotate(4, resample=Image.BICUBIC)
    img.alpha_composite(tmp)
    save(img, name, w, h, seed=seed)


def detail(name, color, seed, w=1000, h=1250, text="FOTO CONTOH"):
    W, H = w * SS, h * SS
    tex = fold_texture(W, H, seed, 2.2, 2.6, 0.5)
    base = np.zeros((H, W, 3), float) + np.array(rgb(color), float)
    img = Image.fromarray(np.clip(base * tex[..., None], 0, 255).astype(np.uint8)).convert("RGBA")
    save(img, name, w, h, text, seed)


def sandal_top(name, color, model, seed, w=1000, h=1250):
    img = canvas(w, h, hexs(mix(CREAM, color, 0.06)), hexs(mix(BLUSH_SOFT, color, 0.16)))
    W, H = img.size
    draw_pair(img, W * 0.5, H * 0.5, H * 0.56, color, model, seed)
    save(img, name, w, h, seed=seed)


def sandal_side(name, color, model, seed, w=1000, h=1250):
    img = canvas(w, h, BLUSH_SOFT, hexs(mix(BLUSH, color, 0.15)))
    W, H = img.size
    d = ImageDraw.Draw(img)
    d.rectangle([0, H * 0.66, W, H], fill=hexs(mix(BLUSH, color, 0.2)))
    draw_side(img, W * 0.12, H * 0.64, W * 0.76, color, model)
    save(img, name, w, h, seed=seed)


def hero(name, w, h, portrait=False):
    img = canvas(w, h, "#F6E3DF", "#E9C5C0")
    W, H = img.size
    d = ImageDraw.Draw(img)
    if portrait:
        arch(d, W * 0.12, H * 0.2, W * 0.88, H, "#F1D4CF")
        u = W * 0.12
        draw_figure(img, W * 0.27, H * 0.5, u * 0.85, "#A47E66", seed=11)
        draw_figure(img, W * 0.73, H * 0.5, u * 0.85, "#7B1E3A", seed=12)
        draw_figure(img, W * 0.5, H * 0.55, u, "#E6B4B0", seed=13)
        draw_pair(img, W * 0.72, H * 0.9, H * 0.13, "#7B1E3A", "balok", 14)
    else:
        for x0 in (0.08, 0.38, 0.68):
            arch(d, W * x0, H * 0.12, W * (x0 + 0.24), H, "#F1D4CF")
        u = H * 0.085
        draw_figure(img, W * 0.24, H * 0.36, u, "#A47E66", seed=11)
        draw_figure(img, W * 0.76, H * 0.36, u, "#7B1E3A", seed=12)
        draw_figure(img, W * 0.5, H * 0.4, u * 1.08, "#E6B4B0", seed=13)
        draw_pair(img, W * 0.88, H * 0.9, H * 0.22, "#7B1E3A", "balok", 14)
    save(img, name, w, h)


def look(name, hijab_color, sandal_color, model, seed, w=1000, h=1250):
    top, bottom = backdrop_for(hijab_color)
    img = canvas(w, h, top, bottom)
    W, H = img.size
    d = ImageDraw.Draw(img)
    arch(d, W * 0.12, H * 0.06, W * 0.88, H, hexs(mix(top, bottom, 0.85)))
    draw_figure(img, W * 0.46, H * 0.32, W * 0.1, hijab_color, seed=seed)
    d.rectangle([0, H * 0.8, W, H], fill=hexs(mix(bottom, hijab_color, 0.08)))
    draw_pair(img, W * 0.68, H * 0.88, H * 0.17, sandal_color, model, seed + 3)
    save(img, name, w, h, seed=seed)


def landscape(name, w=1600, h=1000, text="FOTO DESA MENYUSUL"):
    img = canvas(w, h, "#F9E7E2", "#F2CFC9")
    W, H = img.size
    d = ImageDraw.Draw(img)
    d.ellipse([W * 0.62, H * 0.12, W * 0.78, H * 0.12 + W * 0.16], fill="#F7D9C9")
    layers = [("#D99AA0", 0.45, 1), ("#B8606F", 0.58, 2), ("#8E2F48", 0.7, 3), ("#5E1229", 0.84, 4)]
    for col, base, s in layers:
        rnd = random.Random(s)
        pts = [(0, H)]
        n = 9
        for i in range(n + 1):
            x = W * i / n
            y = H * (base - 0.12 * math.sin(i * 0.9 + s) - rnd.random() * 0.06)
            if s == 1 and i == 4:
                y = H * 0.18  # puncak gunung
            pts.append((x, y))
        pts.append((W, H))
        smooth = [pts[0]] + catmull(pts[1:-1], 10, closed=False) + [pts[-1]]
        d.polygon(smooth, fill=col)
    save(img, name, w, h, text)


def og(name="og-default.jpg"):
    w, h = 1200, 630
    img = canvas(w, h, MAROON, "#5E1229")
    W, H = img.size
    d = ImageDraw.Draw(img)
    try:
        f = ImageFont.truetype(FONT, int(H * 0.2))
        f.set_variation_by_axes([400])
        f2 = ImageFont.truetype(FONT, int(H * 0.06))
        f2.set_variation_by_axes([400])
    except Exception:
        f = f2 = ImageFont.load_default()
    t = "Y U M N I A"
    tw = d.textlength(t, font=f)
    d.text(((W - tw) / 2, H * 0.32), t, font=f, fill=rgb(BLUSH))
    s = "For a better you"
    sw = d.textlength(s, font=f2)
    d.text(((W - sw) / 2, H * 0.62), s, font=f2, fill=rgb(BLUSH))
    img = img.resize((w, h), Image.LANCZOS).convert("RGB")
    img.save(os.path.join(OUT, name), "JPEG", quality=85, optimize=True, progressive=True)
    print("  ", name)


def main():
    os.makedirs(OUT, exist_ok=True)
    data = json.load(open(os.path.join(ROOT, "contoh-data", "produk.json")))
    colors = {c["slug"]: c["hex"] for c in data["warna"]}
    only = sys.argv[1:]

    def want(key):
        return not only or any(o in key for o in only)

    for i, p in enumerate(data["produk"]):
        slug = p["slug"]
        if not want(slug):
            continue
        print(slug)
        first = colors[p["warna"][0]]
        if p["jenis"] == "hijab":
            long_drape = "khimar" in slug
            worn(f"{slug}-pakai.jpg", first, 30 + i, long_drape=long_drape)
            for j, c in enumerate(p["warna"]):
                if "bergo" in slug or "khimar" in slug:
                    flat_hood(f"{slug}-{c}.jpg", colors[c], 40 + i * 7 + j)
                else:
                    pattern = "monogram" if "monogram" in slug else ("plisket" if "plisket" in slug else None)
                    flat_square(f"{slug}-{c}.jpg", colors[c], 40 + i * 7 + j, pattern)
            detail(f"{slug}-detail.jpg", first, 90 + i)
        else:
            for j, c in enumerate(p["warna"]):
                sandal_top(f"{slug}-{c}.jpg", colors[c], p["model"], 60 + i * 7 + j)
            sandal_side(f"{slug}-samping.jpg", first, p["model"], 80 + i)

    if want("hero"):
        print("hero")
        hero("hero.jpg", 1920, 1080)
        hero("hero-hp.jpg", 1080, 1440, portrait=True)
    if want("kategori"):
        print("kategori")
        worn("kategori-hijab.jpg", "#E6B4B0", 5, w=900, h=1200)
        sandal_top("kategori-sandal.jpg", "#7B1E3A", "silang", 6, w=900, h=1200)
    if want("set"):
        print("padu-padan")
        prod = {p["slug"]: p for p in data["produk"]}
        for k, s in enumerate(data["padu_padan"]):
            (h_slug, h_col), (s_slug, s_col) = s["item"][0], s["item"][1]
            look(s["gambar"], colors[h_col], colors[s_col], prod[s_slug]["model"], 100 + k)
    if want("cerita"):
        print("cerita")
        landscape("cerita-sukajadi.jpg")
        worn("pengrajin-1.jpg", "#7B1E3A", 120, w=900, h=1100, garment="#E6D7BF", text="FOTO PENGRAJIN MENYUSUL")
        worn("pengrajin-2.jpg", "#9AAA8F", 121, w=900, h=1100, garment="#F3DAD7", text="FOTO PENGRAJIN MENYUSUL")
        worn("pengrajin-3.jpg", "#A47E66", 122, w=900, h=1100, garment="#FBF7F2", text="FOTO PENGRAJIN MENYUSUL")
        detail("proses-1.jpg", "#B8606F", 130, w=1000, h=1000, text="FOTO PROSES MENYUSUL")
        detail("proses-2.jpg", "#E6B4B0", 131, w=1000, h=1000, text="FOTO PROSES MENYUSUL")
        sandal_side("proses-3.jpg", "#A47E66", "balok", 132, w=1000, h=1000)
        og()


if __name__ == "__main__":
    main()
