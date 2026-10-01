<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeritageItemResource\Pages;
use App\Models\HeritageItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Enums\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HeritageItemResource extends Resource
{
    // --- إعدادات القائمة الجانبية ---
    protected static ?string $navigationIcon = 'heroicon-o-document-text'; // أيقونة القسم
    protected static ?string $navigationLabel = 'HeritageItem'; // اسم القسم في القائمة
    protected static ?int $navigationSort = 3; // ترتيب الظهور (رقم 3 بعد الحسابات)

    // --- تصميم نموذج الإضافة والتعديل (Form) ---
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('معلومات القطعة التراثية')
                    ->description('إدخال البيانات الأساسية للمنشور')
                    ->schema([
                        // حقل اسم القطعة
                        Forms\Components\TextInput::make('name')
                            ->label('اسم القطعة')
                            ->required(),

                        // تقسيم الحقول (سعر وكمية) بجانب بعضهما
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('price')
                                    ->label('السعر')
                                    ->numeric()
                                    ->prefix('$') // العملة
                                    ->required(),

                                Forms\Components\TextInput::make('stock')
                                    ->label('الكمية')
                                    ->numeric()
                                    ->default(1)
                                    ->required(),
                            ]),

                        // القائمة المنسدلة للتصنيفات
                        Forms\Components\Select::make('category')
                            ->label('تصنيف التراث')
                            ->options(Category::options())
                            ->required(),

                        // حقل الوصف الطويل
                        Forms\Components\Textarea::make('description')
                            ->label('الوصف التاريخي')
                            ->rows(3)
                            ->required(),

                        // رفع الصورة وتحديد مسار التخزين
                        Forms\Components\FileUpload::make('image')
                            ->label('صورة القطعة')
                            ->image()
                            ->directory('heritage-images')
                            ->required(),
                    ])
            ]);
    }

    // --- تصميم جدول العرض (Table) ---
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // عرض الصورة بشكل دائري
                Tables\Columns\ImageColumn::make('image')
                    ->label('الصورة')
                    ->circular(),

                // اسم القطعة مع تفعيل البحث والترتيب
                Tables\Columns\TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),

                // عرض التصنيف بشكل ملون (Badge) مع تحويل الكود الإنجليزي لعربي
                Tables\Columns\TextColumn::make('category')
                    ->label('التصنيف')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'clothing' => 'info',    // أزرق
                        'tools'    => 'success', // أخضر
                        'food'     => 'warning', // برتقالي
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => Category::labelFor($state)),

                // حقل تغيير الحالة (Pending/Approved) مباشرة من الجدول
                Tables\Columns\SelectColumn::make('status')
                    ->label('الحالة')
                    ->options([
                        'pending'  => 'قيد الانتظار',
                        'approved' => 'تم القبول',
                        'rejected' => 'مرفوض',
                    ])
                    ->disabled(fn () => auth()->user()?->role === 'publisher'), // الناشر لا يستطيع تغيير الحالة

                // تاريخ الإضافة (مخفي افتراضياً ويمكن إظهاره)
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            // --- قسم الفلاتر (Filters) ---
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('تصفية حسب الصنف')
                    ->options(Category::options()),
                Tables\Filters\TrashedFilter::make()->label('المحذوفات'),
            ])
            // --- العمليات (Edit/Delete) ---
            ->actions([
                Tables\Actions\EditAction::make(), // زر التعديل
                Tables\Actions\DeleteAction::make(), // حذف ناعم
                Tables\Actions\RestoreAction::make(), // استرجاع
            ])
            // --- العمليات الجماعية (Bulk Actions) ---
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(), // حذف مجموعة مختارة
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);

        // الناشر يرى قطعه فقط
        if (auth()->user()?->role === 'publisher') {
            $query->where('user_id', auth()->id());
        }

        return $query;
    }

    // لربط الجداول ببعضها مستقبلاً (مثل التعليقات)
    public static function getRelations(): array
    {
        return [];
    }

    // تعريف مسارات الصفحات (Index, Create, Edit)
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListHeritageItems::route('/'),
            'create' => Pages\CreateHeritageItem::route('/create'),
            'edit'   => Pages\EditHeritageItem::route('/{record}/edit'),
        ];
    }
}
