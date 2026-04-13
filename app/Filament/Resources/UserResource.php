<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\ImageColumn;

class UserResource extends Resource
{
    // أيقونة تدل على مجموعة مستخدمين
protected static ?string $navigationIcon = 'heroicon-o-users';

// لتغيير الاسم بالعربي في القائمة
protected static ?string $navigationLabel = 'Users';

protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            \Filament\Forms\Components\Section::make('معلومات المستخدم الجديد')
                ->schema([
                    \Filament\Forms\Components\TextInput::make('name')
                        ->label('الاسم الكامل')
                        ->required(),

                    \Filament\Forms\Components\TextInput::make('email')
                        ->label('البريد الإلكتروني')
                        ->email()
                        ->unique(ignoreRecord: true) // عشان ما يتكرر الإيميل
                        ->required(),

                    \Filament\Forms\Components\Select::make('role')
                        ->label('الصلاحية (الرتبة)')
                        ->options([
                            'admin' => 'مدير (Admin)',
                            'moderator' => 'مدقق (Moderator)',
                            'publisher' => 'ناشر (Publisher)',
                            'customer' => 'زبون (Customer)',
                        ])
                        ->required(),

                    \Filament\Forms\Components\TextInput::make('password')
                        ->label('كلمة المرور')
                        ->password()
                        ->dehydrated(fn ($state) => filled($state)) // يحفظها فقط إذا كتبت شيئاً
                        ->required(fn (string $context): bool => $context === 'create'), // مطلوبة فقط عند الإنشاء الجديد
                ])
        ]);
}

         public static function table(Table $table): Table
{
    return $table
        ->columns([
            // 1. إضافة عمود الصورة الشخصية بشكل دائري
            \Filament\Tables\Columns\ImageColumn::make('image')
                ->label('الصورة')
                ->circular()
                // تم توحيد الصورة هنا: ضع رابط الصورة الثابتة التي تريدها
                // يمكنك استخدام رابط خارجي أو رابط محلي مثل url('/images/user.png')
                ->defaultImageUrl(url('/images/avatar.png'))

               // هذا السطر للتأكد من أن الفيلد لا يحاول تحميل "فراغ"
               ->state(fn ($record) => $record->image ? url('storage/' . $record->image) : null),

            // 2. عرض الاسم
            \Filament\Tables\Columns\TextColumn::make('name')
                ->label('الاسم')
                ->searchable(),

            // 3. عرض البريد الإلكتروني
            \Filament\Tables\Columns\TextColumn::make('email')
                ->label('البريد الإلكتروني')
                ->searchable(),

            // 4. عرض الرتبة بلون مميز
            \Filament\Tables\Columns\TextColumn::make('role')
                ->label('الرتبة')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'admin' => 'danger',
                    'moderator' => 'warning',
                    'publisher' => 'success',
                    default => 'gray',
                }),

            // 5. تاريخ الإنشاء
            \Filament\Tables\Columns\TextColumn::make('created_at')
                ->label('تاريخ الانضمام')
                ->dateTime()
                ->sortable(),
        ])
        ->filters([
            \Filament\Tables\Filters\SelectFilter::make('role')
                ->label('تصفية حسب الرتبة')
                ->options([
                    'admin' => 'مدير',
                    'moderator' => 'مدقق',
                    'publisher' => 'ناشر',
                ]),
        ])
        ->actions([
            \Filament\Tables\Actions\EditAction::make(),
            \Filament\Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            \Filament\Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
