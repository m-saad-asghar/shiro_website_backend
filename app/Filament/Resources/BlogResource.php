<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use App\Models\Blog;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Filament\Forms\Set;

class BlogResource extends Resource
{
    use Translatable;

    protected static ?string $model = Blog::class;
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationGroup = 'Blog';
    protected static ?string $navigationLabel = 'Blog';
    protected static ?string $pluralModelLabel = 'Blog';
    protected static ?string $modelLabel = 'Blog';
    protected static ?int $navigationSort = 2;

    public static function getTranslatableLocales(): array
    {
        return ['en', 'ar'];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                // ========== BASIC INFORMATION ==========
                Section::make('Basic Information')
                    ->description('Select category, set title and slug.')
                    ->schema([

                        Forms\Components\Select::make('blog_category_id')
                            ->label('Blog Category')
                            ->relationship('blogCategory', 'title')
                            ->required(),

                        TextInput::make('title')
                            ->label('Blog Title')
                            ->afterStateUpdated(function (Set $set, $state) {
                                $set('slug', Str::slug($state));
                            })
                            ->required(),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->readOnly()
                            ->unique(ignoreRecord: true)
                            ->required(),
                    ]),

                // ========== META SEO SECTION ==========
                Section::make('Meta / SEO')
                    ->description('Meta title and description for SEO and social sharing.')
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta Title')
//                            ->maxLength(60)
                            ->required()
                            ->helperText('A short title for SEO.'),

                        Forms\Components\Textarea::make('meta_description')
                            ->label('Meta Description')
//                            ->maxLength(160)
                            ->rows(3)
                            ->required()
                            ->helperText('A short description for SEO .'),
                    ]),

                // ========== CONTENT ==========
                Section::make('Content')
                    ->description('Write the article content and upload main image.')
                    ->schema([
                        Forms\Components\RichEditor::make('description')
                            ->label('Article Content')
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'codeBlock',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ])
                            ->required(),

                        Forms\Components\FileUpload::make('main_image')
                            ->label('Main Image')
                            ->image()
                            ->disk('public')
                            ->directory('images/blog/main_image')
                            ->columnSpanFull(),
                    ]),

                // ========== STATUS & ORDER ==========
                Section::make('Settings')
                    ->description('Manage the article status and display order.')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),

                        Forms\Components\TextInput::make('order')
                            ->label('Order')
                            ->numeric(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable(),

                Tables\Columns\ImageColumn::make('main_image')
                    ->label('Image'),

                Tables\Columns\TextColumn::make('blogCategory.title')
                    ->label('Category')
                    ->searchable(),

                Tables\Columns\TextColumn::make('is_active')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => $state ? 'Active' : 'Draft')
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'warning'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('View'),
                Tables\Actions\EditAction::make()->label('Edit'),
                Tables\Actions\DeleteAction::make()->label('Delete'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Delete'),
                    Tables\Actions\ForceDeleteBulkAction::make()->label('Force Delete'),
                    Tables\Actions\RestoreBulkAction::make()->label('Restore'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'view' => Pages\ViewBlog::route('/{record}'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
    }
}
