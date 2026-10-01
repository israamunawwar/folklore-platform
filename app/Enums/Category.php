<?php

namespace App\Enums;

enum Category: string
{
    case Clothing = 'clothing';
    case Tools = 'tools';
    case Food = 'food';

    public function label(): string
    {
        return match ($this) {
            self::Clothing => 'أزياء وحلي تراثية',
            self::Tools => 'كتب وروايات تراثية',
            self::Food => 'أكلات شعبية',
        };
    }

    /** للاستخدام في قوائم Filament: القيمة => الاسم */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn (self $c) => [$c->value => $c->label()])->all();
    }

    public static function labelFor(?string $value): string
    {
        return self::tryFrom((string) $value)?->label() ?? (string) $value;
    }
}
