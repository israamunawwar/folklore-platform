<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentResource\Pages;
use App\Filament\Resources\CommentResource\RelationManagers;
use App\Models\Comment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CommentResource extends Resource
{
    // أيقونة التعليقات (فقاعات كلام)
protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

// لتغيير الاسم بالعربي في القائمة الجانبية
protected static ?string $navigationLabel = 'Comment';

protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('user.name')->label('المستخدم')->sortable(),
            Tables\Columns\TextColumn::make('heritageItem.name')->label('القطعة التراثية'),
            Tables\Columns\TextColumn::make('comment')->label('التعليق')->limit(50),
            Tables\Columns\TextColumn::make('rating')->label('التقييم (نجوم)')->badge()->color('warning'),

            // عرض الحالة بلون (أصفر للانتظار، أخضر للمقبول)
            Tables\Columns\SelectColumn::make('status')
                ->label('الحالة')
                ->options([
                    'pending' => 'قيد الانتظار',
                    'approved' => 'مقبول',
                ]),
        ])
        ->filters([
            // فلتر عشان المدير يشوف بس اللي "قيد الانتظار" بسرعة
            Tables\Filters\SelectFilter::make('status')
                ->options([
                    'pending' => 'قيد الانتظار',
                    'approved' => 'مقبول',
                ]),
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ]);
}

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
            'create' => Pages\CreateComment::route('/create'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }
public static function canViewAny(): bool
{
    // هاد السطر بيسمح فقط للأدمن والمدقق يشوفوا قسم التعليقات
    return auth()->user()->role === 'admin' || auth()->user()->role === 'moderator';
}

}
