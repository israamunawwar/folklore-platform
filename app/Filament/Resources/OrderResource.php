<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\DB;

class OrderResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Orders';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('order_number')->disabled()->label('رقم الطلب'),
                    Forms\Components\TextInput::make('phone')->label('رقم الهاتف'),
                    Forms\Components\Textarea::make('address')->label('العنوان الكامل')->columnSpanFull(),
                    Forms\Components\Select::make('order_status')
                        ->label('حالة الطلب')
                        ->options([
                            'processing' => 'قيد المعالجة',
                            'shipped' => 'تم الشحن',
                            'completed' => 'تم التسليم',
                            'cancelled' => 'ملغي',
                        ]),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('رقم الطلب')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('اسم الزبون'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('رقم الهاتف'),
                Tables\Columns\TextColumn::make('total_price')
                    ->label('إجمالي المبلغ')
                    ->money('SYP'),
                Tables\Columns\BadgeColumn::make('order_status')
                    ->label('حالة الطلب')
                    ->colors([
                        'primary',
                        'warning' => 'processing',
                        'success' => 'completed',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الطلب')
                    ->dateTime(),
            ])
            ->filters([
                //
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
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user() && auth()->user()->role === 'admin';
    }
}
