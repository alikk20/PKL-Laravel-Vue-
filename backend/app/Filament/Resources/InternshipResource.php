<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Internship;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\InternshipResource\Pages;
use App\Filament\Resources\InternshipResource\RelationManagers;

class InternshipResource extends Resource
{
    protected static ?string $model = Internship::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('siswa_id')
                ->label('Nama Siswa')
                ->relationship('student', 'nama')
                ->required(),
                Forms\Components\Select::make('industri_id')
                ->label('Nama Industri')
                ->relationship('industry', 'nama')
                ->required(),
                Forms\Components\Select::make('guru_id')
                ->label('Nama Guru')
                ->relationship('teacher', 'nama')
                ->required(),
                Forms\Components\Fieldset::make('Periode PKL')
                ->schema([
                    DatePicker::make('mulai')
                        ->label('Mulai Dari'),
                    DatePicker::make('selesai')
                        ->label('Sampai'),
                ])
                ->columns(2)    
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('student.image')->circular()
                ->label('Profil')
                ->searchable(),
                Tables\Columns\TextColumn::make('student.nama')
                ->label('Nama Siswa')
                ->wrap()
                ->searchable(),
                Tables\Columns\TextColumn::make('student.nis')
                ->label('NIS')
                ->searchable(),
                Tables\Columns\TextColumn::make('industry.nama')
                ->label('Nama Industri')
                ->wrap()
                ->searchable(),
                Tables\Columns\TextColumn::make('teacher.nama')
                ->label('Guru Pembimbing')
                ->wrap()
                ->searchable(),
                Tables\Columns\TextColumn::make('mulai')
                ->label('Mulai Dari')
                ->date('d F Y')
                ->searchable(),
                Tables\Columns\TextColumn::make('selesai')
                ->label('Selesai')
                ->date('d F Y')
                ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageInternships::route('/'),
        ];
    }
}
