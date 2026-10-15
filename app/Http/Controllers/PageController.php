<?php

namespace App\Http\Controllers;

use App\Mail\MessageReceived;
use App\Models\Album;
use App\Models\Attraction;
use App\Models\Faq;
use App\Models\Lead;
use App\Models\Message;
use App\Models\Official;
use App\Models\Package;
use App\Models\Partner;
use App\Models\Photo;
use App\Models\Post;
use App\Models\ProfileSection;
use App\Models\ProfileStat;
use App\Models\Testimonial;
use App\Models\Umkm;
use App\Support\Notifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PageController extends Controller
{
    public function home()
    {
        $albumPhotos = Photo::query()->where('photoable_type', Album::class)
            ->whereIn('photoable_id', Album::query()->where('is_active', true)->select('id'))
            ->orderByRaw('path is null')->orderBy('sort')->orderBy('id')->get();

        return view('pages.home', [
            'packages' => Package::query()->active()->with('tiers')->get(),
            'attractions' => Attraction::query()->active()->get(),
            'umkms' => Umkm::query()->active()->where('is_featured', true)->take(6)->get(),
            'partners' => Partner::query()->active()->where('is_featured', true)->with(['offers' => fn ($q) => $q->where('is_active', true)])->get(),
            'storyPhotos' => $albumPhotos->take(5)->values(),
            'photos' => $albumPhotos->slice(5, 10)->values(),
            'marquee' => $albumPhotos->slice(15)->merge(Photo::query()->whereIn('photoable_type', [Attraction::class, Package::class])->orderByRaw('path is null')->orderBy('id')->take(30)->get())->take(24),
            'team' => Official::query()->orderBy('sort')->take(6)->get(),
            'counts' => [
                'attractions' => Attraction::query()->where('is_active', true)->count(),
                'umkm' => Umkm::query()->where('is_active', true)->count(),
                'partners' => Partner::query()->where('is_active', true)->count(),
                'packages' => Package::query()->where('is_active', true)->count(),
            ],
            'testimonials' => Testimonial::query()->approved()->where('is_featured', true)->take(6)->get(),
            'posts' => Post::query()->published()->take(3)->get(),
        ]);
    }

    public function profile()
    {
        return view('pages.profile', [
            'stats' => ProfileStat::query()->orderBy('sort')->get()->groupBy('group'),
            'sections' => ProfileSection::query()->where('is_active', true)->orderBy('sort')->get(),
        ]);
    }

    public function team()
    {
        return view('pages.team', [
            'team' => Official::query()->orderBy('sort')->get(),
        ]);
    }

    public function about()
    {
        return view('pages.about', [
            'attractions' => Attraction::query()->active()->take(8)->get(),
        ]);
    }

    public function faq()
    {
        return view('pages.faq', [
            'faqs' => Faq::query()->active()->get()->groupBy('category'),
        ]);
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function sendMessage(Request $request)
    {
        $data = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:120'],
            'contact' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:3000'],
            'website' => ['prohibited'],
        ], [], [
            'name' => __('site.contact.name'),
            'contact' => __('site.contact.contact'),
            'message' => __('site.contact.message'),
        ])->validate();

        $message = Message::create([
            'name' => $data['name'],
            'contact' => $data['contact'],
            'message' => $data['message'],
            'locale' => app()->getLocale(),
        ]);

        Lead::create([
            'name' => $message->name,
            'phone' => str_contains($message->contact, '@') ? null : $message->contact,
            'email' => str_contains($message->contact, '@') ? $message->contact : null,
            'source' => 'contact',
            'interest' => \Illuminate\Support\Str::limit($message->message, 120),
            'message_id' => $message->id,
            'locale' => $message->locale,
        ]);

        Notifier::admins(new MessageReceived($message));

        return back()->with('status', __('site.contact.sent'));
    }

    public function reviews()
    {
        return view('pages.reviews', [
            'testimonials' => Testimonial::query()->approved()->paginate(12),
        ]);
    }

    public function storeReview(Request $request)
    {
        $data = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:120'],
            'organization' => ['nullable', 'string', 'max:150'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'content' => ['required', 'string', 'min:10', 'max:1500'],
            'website' => ['prohibited'],
        ], [], [
            'name' => __('site.reviews.name'),
            'organization' => __('site.reviews.organization'),
            'rating' => __('site.reviews.rating'),
            'content' => __('site.reviews.content'),
        ])->validate();

        Testimonial::create([
            'name' => $data['name'],
            'organization' => $data['organization'] ?? null,
            'rating' => $data['rating'],
            'content' => $data['content'],
            'locale' => app()->getLocale(),
            'is_approved' => false,
        ]);

        return back()->with('status', __('site.reviews.sent'));
    }
}
