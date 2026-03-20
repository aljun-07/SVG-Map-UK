<?php
defined( 'ABSPATH' ) || exit;

/**
 * Default region data shipped with the plugin.
 * Each region keyed by its SVG data-region slug.
 */
class UK_Map_Data {

    /**
     * Returns the default region configuration array.
     *
     * @return array<string, array>
     */
    public static function defaults(): array {
        return [
            'scotland' => [
                'name'        => 'Scotland',
                'description' => 'Scotland is a country in northwestern Europe and one of the four nations of the United Kingdom.',
                'color'       => '#4a90d9',
                'link'        => 'https://www.visitscotland.com/',
                'link_label'  => 'Visit Scotland',
                'stats'       => [
                    'Capital'    => 'Edinburgh',
                    'Population' => '5.5 million',
                    'Area'       => '77,933 km²',
                ],
            ],
            'northern-ireland' => [
                'name'        => 'Northern Ireland',
                'description' => 'Northern Ireland is a part of the United Kingdom located in the northeast of the island of Ireland.',
                'color'       => '#5ba05b',
                'link'        => 'https://discovernorthernireland.com/',
                'link_label'  => 'Discover NI',
                'stats'       => [
                    'Capital'    => 'Belfast',
                    'Population' => '1.9 million',
                    'Area'       => '13,843 km²',
                ],
            ],
            'wales' => [
                'name'        => 'Wales',
                'description' => 'Wales is a country that is part of the United Kingdom, bordered by England to the east and the Irish Sea to the north and west.',
                'color'       => '#c94040',
                'link'        => 'https://www.visitwales.com/',
                'link_label'  => 'Visit Wales',
                'stats'       => [
                    'Capital'    => 'Cardiff',
                    'Population' => '3.2 million',
                    'Area'       => '20,779 km²',
                ],
            ],
            'north-east' => [
                'name'        => 'North East England',
                'description' => 'The North East is the most northerly and least populated of the nine regions of England.',
                'color'       => '#7b52ab',
                'link'        => '',
                'link_label'  => '',
                'stats'       => [
                    'Population' => '2.6 million',
                    'Largest city' => 'Newcastle',
                ],
            ],
            'north-west' => [
                'name'        => 'North West England',
                'description' => 'The North West contains major cities including Manchester, Liverpool and Preston.',
                'color'       => '#4a90d9',
                'link'        => '',
                'link_label'  => '',
                'stats'       => [
                    'Population'   => '7.4 million',
                    'Largest city' => 'Manchester',
                ],
            ],
            'yorkshire' => [
                'name'        => 'Yorkshire and The Humber',
                'description' => 'Home to the cities of Leeds, Sheffield, Bradford and Hull.',
                'color'       => '#d97b4a',
                'link'        => '',
                'link_label'  => '',
                'stats'       => [
                    'Population'   => '5.5 million',
                    'Largest city' => 'Leeds',
                ],
            ],
            'east-midlands' => [
                'name'        => 'East Midlands',
                'description' => 'The East Midlands is known for Nottingham, Derby, Leicester and the historic county of Lincolnshire.',
                'color'       => '#4a90d9',
                'link'        => '',
                'link_label'  => '',
                'stats'       => [
                    'Population'   => '4.9 million',
                    'Largest city' => 'Nottingham',
                ],
            ],
            'west-midlands' => [
                'name'        => 'West Midlands',
                'description' => 'The West Midlands region includes Birmingham, the UK\'s second largest city, along with Coventry and Wolverhampton.',
                'color'       => '#52a0a0',
                'link'        => '',
                'link_label'  => '',
                'stats'       => [
                    'Population'   => '5.9 million',
                    'Largest city' => 'Birmingham',
                ],
            ],
            'east-of-england' => [
                'name'        => 'East of England',
                'description' => 'The East of England region includes Norfolk, Suffolk, Essex, Hertfordshire, Bedfordshire and Cambridgeshire.',
                'color'       => '#a0a052',
                'link'        => '',
                'link_label'  => '',
                'stats'       => [
                    'Population'   => '6.3 million',
                    'Largest city' => 'Norwich',
                ],
            ],
            'london' => [
                'name'        => 'London',
                'description' => 'London is the capital and largest city of the United Kingdom, a global hub of finance, culture and history.',
                'color'       => '#c94040',
                'link'        => 'https://www.visitlondon.com/',
                'link_label'  => 'Visit London',
                'stats'       => [
                    'Population' => '9 million',
                    'Area'       => '1,572 km²',
                    'Boroughs'   => '32',
                ],
            ],
            'south-east' => [
                'name'        => 'South East England',
                'description' => 'The South East is the most populous region of England outside London, and includes Kent, Sussex and Surrey.',
                'color'       => '#4a90d9',
                'link'        => '',
                'link_label'  => '',
                'stats'       => [
                    'Population'   => '9.2 million',
                    'Largest city' => 'Southampton',
                ],
            ],
            'south-west' => [
                'name'        => 'South West England',
                'description' => 'The South West includes Cornwall, Devon, Somerset and Dorset — famous for its rugged coastline and countryside.',
                'color'       => '#7ba052',
                'link'        => '',
                'link_label'  => '',
                'stats'       => [
                    'Population'   => '5.7 million',
                    'Largest city' => 'Bristol',
                ],
            ],
        ];
    }

    /**
     * Returns the list of all known region slugs.
     *
     * @return string[]
     */
    public static function slugs(): array {
        return array_keys( self::defaults() );
    }
}
