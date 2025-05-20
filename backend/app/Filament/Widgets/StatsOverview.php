<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use App\Models\Industry;
use App\Models\Internship;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Jumlah Siswa', Student::count()),
            Stat::make('Jumlah PKL', Internship::count()),
            Stat::make('Jumlah Industri', Industry::count()),
        ];
    }
}
