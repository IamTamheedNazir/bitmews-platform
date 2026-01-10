<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TokenResource\Pages;
use App\Models\Token;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TokenResource extends Resource
{
    protected static ?string $model = Token::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'Crypto Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Token Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('symbol')
                            ->required()
                            ->maxLength(20)
                            ->uppercase(),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Forms\Components\Select::make('blockchain_id')
                            ->relationship('blockchain', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('contract_address')
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('logo_url')
                            ->image()
                            ->directory('tokens'),
                    ])->columns(2),

                Forms\Components\Section::make('Details')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->maxLength(1000),
                        Forms\Components\TextInput::make('website')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\KeyValue::make('social_links')
                            ->keyLabel('Platform')
                            ->valueLabel('URL'),
                        Forms\Components\Select::make('category')
                            ->options([
                                'defi' => 'DeFi',
                                'nft' => 'NFT',
                                'gaming' => 'Gaming',
                                'metaverse' => 'Metaverse',
                                'layer1' => 'Layer 1',
                                'layer2' => 'Layer 2',
                            ])
                            ->searchable(),
                        Forms\Components\TagsInput::make('tags'),
                    ])->columns(2),

                Forms\Components\Section::make('Market Data')
                    ->schema([
                        Forms\Components\TextInput::make('current_price')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\TextInput::make('market_cap')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\TextInput::make('volume_24h')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\TextInput::make('circulating_supply')
                            ->numeric(),
                        Forms\Components\TextInput::make('total_supply')
                            ->numeric(),
                        Forms\Components\TextInput::make('max_supply')
                            ->numeric(),
                        Forms\Components\TextInput::make('market_cap_rank')
                            ->numeric(),
                        Forms\Components\TextInput::make('risk_score')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100),
                    ])->columns(4),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_verified')
                            ->default(false),
                        Forms\Components\Toggle::make('is_featured')
                            ->default(false),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                        Forms\Components\DateTimePicker::make('listed_at'),
                    ])->columns(4),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo_url')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('symbol')
                    ->searchable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('blockchain.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_price')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('price_change_24h')
                    ->suffix('%')
                    ->color(fn ($state) => $state >= 0 ? 'success' : 'danger')
                    ->sortable(),
                Tables\Columns\TextColumn::make('market_cap')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('volume_24h')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_verified')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('views_count')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('blockchain')
                    ->relationship('blockchain', 'name'),
                Tables\Filters\TernaryFilter::make('is_verified'),
                Tables\Filters\TernaryFilter::make('is_featured'),
                Tables\Filters\TernaryFilter::make('is_active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('market_cap_rank', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTokens::route('/'),
            'create' => Pages\CreateToken::route('/create'),
            'edit' => Pages\EditToken::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::active()->count();
    }
}
