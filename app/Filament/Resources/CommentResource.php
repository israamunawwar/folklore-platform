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
                Forms\Components\Section::make()->schema([
                    Forms\Components\Placeholder::make('author')
                        ->label('المستخدم')
                        ->content(fn (?\App\Models\Comment $record) => $record?->user?->name),
                    Forms\Components\Placeholder::make('item')
                        ->label('القطعة التراثية')
                        ->content(fn (?\App\Models\Comment $record) => $record?->heritageItem?->name),
                    Forms\Components\Textarea::make('comment')->label('التعليق')->disabled()->columnSpanFull(),
                    Forms\Components\TextInput::make('rating')->label('التقييم')->disabled(),
                    Forms\Components\Select::make('status')
                        ->label('الحالة')
                        ->options(self::statuses())
                        ->required(),
                ])->columns(2),
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
                ->options(self::statuses()),
        ])
        ->filters([
            // فلتر عشان المدير يشوف بس اللي "قيد الانتظار" بسرعة
            Tables\Filters\SelectFilter::make('status')
                ->label('الحالة')
                ->options(self::statuses()),
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
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }

    public static function statuses(): array
    {
        return [
            'pending' => 'قيد الانتظار',
            'approved' => 'مقبول',
            'rejected' => 'مرفوض',
        ];
    }
}
