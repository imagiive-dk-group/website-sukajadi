<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Filament\Support\Fields;
use App\Models\Lead;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListLeads extends ListRecords
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Prospek manual'),
        ];
    }

    public static function followUpQuery(Builder $query): Builder
    {
        return $query
            ->whereIn('status', LeadResource::OPEN_STATUSES)
            ->whereNotNull('follow_up_at')
            ->whereDate('follow_up_at', '<=', today());
    }

    public function getTabs(): array
    {
        $tabs = ['all' => Tab::make('Semua')];

        $labels = ['new' => 'Baru', 'contacted' => 'Dihubungi', 'negotiating' => 'Negosiasi', 'won' => 'Berhasil', 'lost' => 'Batal'];

        foreach ($labels as $status => $label) {
            $tab = Tab::make($label)
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('status', $status));

            if ($status === 'new') {
                $tab->badge(fn (): int => Lead::query()->where('status', 'new')->count())
                    ->badgeColor(Fields::leadStatusColor('new'));
            }

            $tabs[$status] = $tab;
        }

        $tabs['follow_up'] = Tab::make('Follow-up hari ini/terlambat')
            ->badge(fn (): ?int => static::followUpQuery(Lead::query())->count() ?: null)
            ->badgeColor('danger')
            ->modifyQueryUsing(fn (Builder $query): Builder => static::followUpQuery($query));

        return $tabs;
    }
}
