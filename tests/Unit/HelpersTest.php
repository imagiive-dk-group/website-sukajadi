<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    public function test_localized_falls_back_to_indonesian(): void
    {
        $this->assertSame('Halo', localized(['id' => 'Halo', 'en' => ''], 'en'));
        $this->assertSame('Hello', localized(['id' => 'Halo', 'en' => 'Hello'], 'en'));
        $this->assertSame('plain', localized('plain', 'en'));
        $this->assertNull(localized(['id' => '', 'en' => null], 'en'));
    }

    public function test_whatsapp_numbers_are_normalized(): void
    {
        $this->assertSame('6285775194575', wa_number('+62 857-7519-4575'));
        $this->assertSame('6285775194575', wa_number('0857 7519 4575'));
        $this->assertNull(wa_number(''));
    }

    public function test_rupiah_format(): void
    {
        $this->assertSame('Rp125.000', rupiah(125000));
        $this->assertSame('Rp0', rupiah(null));
    }
}
