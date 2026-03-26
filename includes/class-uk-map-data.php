<?php
defined( 'ABSPATH' ) || exit;

/**
 * All UK region codes sourced from simplemaps countrymap mapdata.js.
 * Keys are ISO 3166-2:GB codes used by countrymap.js as state identifiers.
 */
class UK_Map_Data {

    /**
     * Default region data for all 227 GB regions.
     *
     * @return array<string, array>
     */
    public static function defaults(): array {
        return [
            'GBABD' => [ 'name' => 'Aberdeenshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBABE' => [ 'name' => 'Aberdeen', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBAGB' => [ 'name' => 'Argyll and Bute', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBAGY' => [ 'name' => 'Anglesey', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBANS' => [ 'name' => 'Angus', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBANT' => [ 'name' => 'Antrim', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBARD' => [ 'name' => 'Ards', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBARM' => [ 'name' => 'Armagh', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBBAS' => [ 'name' => 'Bath and North East Somerset', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBBBD' => [ 'name' => 'Blackburn with Darwen', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBBDF' => [ 'name' => 'Bedford', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBBDG' => [ 'name' => 'Barking and Dagenham', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBBEN' => [ 'name' => 'Brent', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBBEX' => [ 'name' => 'Bexley', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBBFS' => [ 'name' => 'Belfast', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBBGE' => [ 'name' => 'Bridgend', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBBGW' => [ 'name' => 'Blaenau Gwent', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBBIR' => [ 'name' => 'Birmingham', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBBKM' => [ 'name' => 'Buckinghamshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBBLA' => [ 'name' => 'Ballymena', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBBLY' => [ 'name' => 'Ballymoney', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBBMH' => [ 'name' => 'Bournemouth', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBBNB' => [ 'name' => 'Banbridge', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBBNE' => [ 'name' => 'Barnet', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBBNH' => [ 'name' => 'Brighton and Hove', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBBNS' => [ 'name' => 'Barnsley', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBBOL' => [ 'name' => 'Bolton', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBBPL' => [ 'name' => 'Blackpool', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBBRC' => [ 'name' => 'Bracknell Forest', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBBRD' => [ 'name' => 'Bradford', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBBRY' => [ 'name' => 'Bromley', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBBST' => [ 'name' => 'Bristol', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBBUR' => [ 'name' => 'Bury', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBCAM' => [ 'name' => 'Cambridgeshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBCAY' => [ 'name' => 'Caerphilly', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBCBF' => [ 'name' => 'Central Bedfordshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBCGN' => [ 'name' => 'Ceredigion', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBCGV' => [ 'name' => 'Craigavon', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBCHE' => [ 'name' => 'Cheshire East', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBCHW' => [ 'name' => 'Cheshire West and Chester', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBCKF' => [ 'name' => 'Carrickfergus', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBCKT' => [ 'name' => 'Mid Ulster', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBCLD' => [ 'name' => 'Calderdale', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBCLK' => [ 'name' => 'Clackmannanshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBCLR' => [ 'name' => 'Coleraine', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBCMA' => [ 'name' => 'Cumbria', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBCMD' => [ 'name' => 'Camden', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBCMN' => [ 'name' => 'Carmarthenshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBCON' => [ 'name' => 'Cornwall', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBCOV' => [ 'name' => 'Coventry', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBCRF' => [ 'name' => 'Cardiff', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBCRY' => [ 'name' => 'Croydon', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBCSR' => [ 'name' => 'Castlereagh', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBCWY' => [ 'name' => 'Conwy', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBDAL' => [ 'name' => 'Darlington', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBDBY' => [ 'name' => 'Derbyshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBDEN' => [ 'name' => 'Denbighshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBDER' => [ 'name' => 'Derby', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBDEV' => [ 'name' => 'Devon', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBDGN' => [ 'name' => 'Dungannon', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBDGY' => [ 'name' => 'Dumfries and Galloway', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBDNC' => [ 'name' => 'Doncaster', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBDND' => [ 'name' => 'Dundee', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBDOR' => [ 'name' => 'Dorset', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBDOW' => [ 'name' => 'Down', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBDRY' => [ 'name' => 'Derry', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBDUD' => [ 'name' => 'Dudley', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBDUR' => [ 'name' => 'Durham', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBEAL' => [ 'name' => 'Ealing', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBEAY' => [ 'name' => 'East Ayrshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBEDH' => [ 'name' => 'Edinburgh', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBEDU' => [ 'name' => 'East Dunbartonshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBELN' => [ 'name' => 'East Lothian', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBELS' => [ 'name' => 'Eilean Siar', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBENF' => [ 'name' => 'Enfield', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBERW' => [ 'name' => 'East Renfrewshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBERY' => [ 'name' => 'East Riding of Yorkshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBESS' => [ 'name' => 'Essex', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBESX' => [ 'name' => 'East Sussex', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBFAL' => [ 'name' => 'Falkirk', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBFER' => [ 'name' => 'Fermanagh', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBFIF' => [ 'name' => 'Fife', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBFLN' => [ 'name' => 'Flintshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBGAT' => [ 'name' => 'Gateshead', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBGLG' => [ 'name' => 'Glasgow', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBGLS' => [ 'name' => 'Gloucestershire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBGRE' => [ 'name' => 'Greenwich', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBGWN' => [ 'name' => 'Gwynedd', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBHAL' => [ 'name' => 'Halton', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBHAM' => [ 'name' => 'Hampshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBHAV' => [ 'name' => 'Havering', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBHCK' => [ 'name' => 'Hackney', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBHEF' => [ 'name' => 'Herefordshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBHIL' => [ 'name' => 'Hillingdon', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBHLD' => [ 'name' => 'Highland', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBHMF' => [ 'name' => 'Hammersmith and Fulham', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBHNS' => [ 'name' => 'Hounslow', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBHPL' => [ 'name' => 'Hartlepool', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBHRT' => [ 'name' => 'Hertfordshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBHRW' => [ 'name' => 'Harrow', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBHRY' => [ 'name' => 'Haringey', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBIOS' => [ 'name' => 'Isles of Scilly', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBIOW' => [ 'name' => 'Isle of Wight', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBISL' => [ 'name' => 'Islington', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBIVC' => [ 'name' => 'Inverclyde', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBKEC' => [ 'name' => 'Kensington and Chelsea', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBKEN' => [ 'name' => 'Kent', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBKHL' => [ 'name' => 'Kingston upon Hull', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBKIR' => [ 'name' => 'Kirklees', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBKTT' => [ 'name' => 'Kingston upon Thames', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBKWL' => [ 'name' => 'Knowsley', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBLAN' => [ 'name' => 'Lancashire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBLBH' => [ 'name' => 'Lambeth', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBLCE' => [ 'name' => 'Leicester', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBLDS' => [ 'name' => 'Leeds', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBLEC' => [ 'name' => 'Leicestershire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBLEW' => [ 'name' => 'Lewisham', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBLIN' => [ 'name' => 'Lincolnshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBLIV' => [ 'name' => 'Liverpool', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBLMV' => [ 'name' => 'Limavady', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBLND' => [ 'name' => 'City of London', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBLRN' => [ 'name' => 'Larne', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBLSB' => [ 'name' => 'Lisburn', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBLUT' => [ 'name' => 'Luton', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBMAN' => [ 'name' => 'Manchester', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBMDB' => [ 'name' => 'Middlesbrough', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBMDW' => [ 'name' => 'Medway', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBMFT' => [ 'name' => 'Magherafelt', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBMIK' => [ 'name' => 'Milton Keynes', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBMLN' => [ 'name' => 'Midlothian', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBMON' => [ 'name' => 'Monmouthshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBMRT' => [ 'name' => 'Merton', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBMRY' => [ 'name' => 'Moray', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBMTY' => [ 'name' => 'Merthyr Tydfil', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBMYL' => [ 'name' => 'Moyle', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBNAY' => [ 'name' => 'North Ayrshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBNBL' => [ 'name' => 'Northumberland', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBNDN' => [ 'name' => 'North Down', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBNEL' => [ 'name' => 'North East Lincolnshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBNET' => [ 'name' => 'Newcastle upon Tyne', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBNFK' => [ 'name' => 'Norfolk', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBNGM' => [ 'name' => 'Nottingham', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBNLK' => [ 'name' => 'North Lanarkshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBNLN' => [ 'name' => 'North Lincolnshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBNSM' => [ 'name' => 'North Somerset', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBNTA' => [ 'name' => 'Newtownabbey', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBNTH' => [ 'name' => 'Northamptonshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBNTL' => [ 'name' => 'Neath Port Talbot', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBNTT' => [ 'name' => 'Nottinghamshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBNTY' => [ 'name' => 'North Tyneside', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBNWM' => [ 'name' => 'Newham', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBNWP' => [ 'name' => 'Newport', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBNYK' => [ 'name' => 'North Yorkshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBNYM' => [ 'name' => 'Newry and Mourne', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBOLD' => [ 'name' => 'Oldham', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBOMH' => [ 'name' => 'Omagh', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBORK' => [ 'name' => 'Orkney', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBOXF' => [ 'name' => 'Oxfordshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBPEM' => [ 'name' => 'Pembrokeshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBPKN' => [ 'name' => 'Perthshire and Kinross', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBPLY' => [ 'name' => 'Plymouth', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBPOL' => [ 'name' => 'Poole', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBPOR' => [ 'name' => 'Portsmouth', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBPOW' => [ 'name' => 'Powys', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBPTE' => [ 'name' => 'Peterborough', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBRCC' => [ 'name' => 'Redcar and Cleveland', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBRCH' => [ 'name' => 'Rochdale', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBRCT' => [ 'name' => 'Rhondda, Cynon, Taff', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBRDB' => [ 'name' => 'Redbridge', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBRDG' => [ 'name' => 'Reading', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBRFW' => [ 'name' => 'Renfrewshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBRIC' => [ 'name' => 'Richmond upon Thames', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBROT' => [ 'name' => 'Rotherham', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBRUT' => [ 'name' => 'Rutland', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBSAW' => [ 'name' => 'Sandwell', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBSAY' => [ 'name' => 'South Ayrshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBSCB' => [ 'name' => 'Scottish Borders', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBSFK' => [ 'name' => 'Suffolk', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBSFT' => [ 'name' => 'Sefton', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBSGC' => [ 'name' => 'South Gloucestershire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBSHF' => [ 'name' => 'Sheffield', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBSHN' => [ 'name' => 'Merseyside', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBSHR' => [ 'name' => 'Shropshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBSKP' => [ 'name' => 'Stockport', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBSLF' => [ 'name' => 'Salford', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBSLG' => [ 'name' => 'Slough', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBSLK' => [ 'name' => 'South Lanarkshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBSND' => [ 'name' => 'Sunderland', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBSOL' => [ 'name' => 'Solihull', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBSOM' => [ 'name' => 'Somerset', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBSOS' => [ 'name' => 'Southend-on-Sea', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBSRY' => [ 'name' => 'Surrey', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBSTB' => [ 'name' => 'Strabane', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBSTE' => [ 'name' => 'Stoke-on-Trent', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBSTG' => [ 'name' => 'Stirling', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBSTH' => [ 'name' => 'Southampton', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBSTN' => [ 'name' => 'Sutton', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBSTS' => [ 'name' => 'Staffordshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBSTT' => [ 'name' => 'Stockton-on-Tees', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBSTY' => [ 'name' => 'South Tyneside', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBSWA' => [ 'name' => 'Swansea', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBSWD' => [ 'name' => 'Swindon', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBSWK' => [ 'name' => 'Southwark', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBTAM' => [ 'name' => 'Tameside', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBTFW' => [ 'name' => 'Telford and Wrekin', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBTHR' => [ 'name' => 'Thurrock', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBTOB' => [ 'name' => 'Torbay', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBTOF' => [ 'name' => 'Torfaen', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBTRF' => [ 'name' => 'Trafford', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBTWH' => [ 'name' => 'Tower Hamlets', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBVGL' => [ 'name' => 'Vale of Glamorgan', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBWAR' => [ 'name' => 'Warwickshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBWBK' => [ 'name' => 'West Berkshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBWDU' => [ 'name' => 'West Dunbartonshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBWFT' => [ 'name' => 'Waltham Forest', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBWGN' => [ 'name' => 'Wigan', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBWIL' => [ 'name' => 'Wiltshire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBWKF' => [ 'name' => 'Wakefield', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBWLL' => [ 'name' => 'Walsall', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBWLN' => [ 'name' => 'West Lothian', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBWLV' => [ 'name' => 'Wolverhampton', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBWND' => [ 'name' => 'Wandsworth', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBWNM' => [ 'name' => 'Windsor and Maidenhead', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBWOK' => [ 'name' => 'Wokingham', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBWOR' => [ 'name' => 'Worcestershire', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBWRL' => [ 'name' => 'Wirral', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBWRT' => [ 'name' => 'Warrington', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBWRX' => [ 'name' => 'Wrexham', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBWSM' => [ 'name' => 'Westminster', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBWSX' => [ 'name' => 'West Sussex', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBYOR' => [ 'name' => 'York', 'color' => '', 'hover_color' => '', 'projects' => [] ],
            'GBZET' => [ 'name' => 'Shetland Islands', 'color' => '', 'hover_color' => '', 'projects' => [] ],
        ];
    }

    /**
     * Returns just the region codes.
     *
     * @return string[]
     */
    public static function slugs(): array {
        return array_keys( self::defaults() );
    }

    /**
     * Returns code => name mapping (used for the admin list).
     *
     * @return array<string,string>
     */
    public static function region_names(): array {
        $out = [];
        foreach ( self::defaults() as $code => $r ) {
            $out[ $code ] = $r['name'];
        }
        return $out;
    }

    /**
     * Merges saved option data with defaults so every region always has all keys.
     *
     * @param array $saved
     * @return array
     */
    public static function merge_with_defaults( array $saved ): array {
        $defaults = self::defaults();
        foreach ( $defaults as $code => $def ) {
            if ( isset( $saved[ $code ] ) ) {
                $defaults[ $code ] = array_merge( $def, $saved[ $code ] );
                // Ensure projects is always an array.
                if ( ! is_array( $defaults[ $code ]['projects'] ) ) {
                    $defaults[ $code ]['projects'] = [];
                }
            }
        }
        return $defaults;
    }
}
