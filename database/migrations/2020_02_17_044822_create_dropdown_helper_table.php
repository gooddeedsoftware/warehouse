<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\DropdownHelper;

class CreateDropdownHelperTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('dropdown_helper')) {
            Schema::create('dropdown_helper', function (Blueprint $table) {
                $table->string('group_code', 50);
                $table->string('group_name', 100);
                $table->string('key_code', 50);
                $table->string('value_en', 100);
                $table->string('value_no', 100);
            });
        }
        $this->seed();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('dropdown_helper');
    }

    public function seed()
    {
        \DB::table('dropdown_helper')->delete();
        \DB::table('dropdown_helper')->insert(array(
            // customer order status
            array(
                'group_code' => '005',
                'group_name' => 'Order Status',
                'key_code'   => '1',
                'value_en'   => 'Open',
                'value_no'   => 'Tilbud',
            ),
            array(
                'group_code' => '005',
                'group_name' => 'Order Status',
                'key_code'   => '2',
                'value_en'   => 'In Progress',
                'value_no'   => 'Ordre',
            ),
            array(
                'group_code' => '005',
                'group_name' => 'Order Status',
                'key_code'   => '3',
                'value_en'   => 'To be invoiced',
                'value_no'   => 'Til fakturering',
            ),
            array(
                'group_code' => '005',
                'group_name' => 'Order Status',
                'key_code'   => '4',
                'value_en'   => 'Invoiced',
                'value_no'   => 'Fakturert',
            ),
            array(
                'group_code' => '005',
                'group_name' => 'Order Status',
                'key_code'   => '5',
                'value_en'   => 'Archive',
                'value_no'   => 'Arkiv',
            ),
            //Genrral status
            array(
                'group_code' => '002',
                'group_name' => 'General Status',
                'key_code'   => '01',
                'value_en'   => 'Yes',
                'value_no'   => 'Ja',
            ),
            array(
                'group_code' => '002',
                'group_name' => 'General Status',
                'key_code'   => '02',
                'value_en'   => 'No',
                'value_no'   => 'Nei',
            ),

            //priorties
            array(
                'group_code' => '006',
                'group_name' => 'Priority',
                'key_code'   => '01',
                'value_en'   => 'Low',
                'value_no'   => 'Lav',
            ),
            array(
                'group_code' => '006',
                'group_name' => 'Priority',
                'key_code'   => '02',
                'value_en'   => 'Normal',
                'value_no'   => 'Normal',
            ),
            array(
                'group_code' => '006',
                'group_name' => 'Priority',
                'key_code'   => '03',
                'value_en'   => 'High',
                'value_no'   => 'Høy',
            ),

            //Order category
            array(
                'group_code' => '007',
                'group_name' => 'Order Category',
                'key_code'   => '01',
                'value_en'   => 'Serviceorder',
                'value_no'   => 'Serviceordre',
            ),
            array(
                'group_code' => '007',
                'group_name' => 'Order Category',
                'key_code'   => '02',
                'value_en'   => 'Projectorder',
                'value_no'   => 'Prosjektordre',
            ),

            array(
                'group_code' => '007',
                'group_name' => 'Order Category',
                'key_code'   => '03',
                'value_en'   => 'Sales',
                'value_no'   => 'Salg',
            ),

            array(
                'group_code' => '007',
                'group_name' => 'Order Category',
                'key_code'   => '04',
                'value_en'   => 'Rental',
                'value_no'   => 'Utleie',
            ),

            array(
                'group_code' => '007',
                'group_name' => 'Order Category',
                'key_code'   => '05',
                'value_en'   => 'Installation',
                'value_no'   => 'Montering',
            ),

            array(
                'group_code' => '007',
                'group_name' => 'Order Category',
                'key_code'   => '06',
                'value_en'   => 'Internal',
                'value_no'   => 'Internordre',
            ),

            array(
                'group_code' => '007',
                'group_name' => 'Order Category',
                'key_code'   => '07',
                'value_en'   => 'Support',
                'value_no'   => 'Support avt',
            ),

            //Pmt terms
            array(
                'group_code' => '009',
                'group_name' => 'PMT Terms',
                'key_code'   => '10',
                'value_en'   => '10 Days',
                'value_no'   => '10 Dager',
            ),
            array(
                'group_code' => '009',
                'group_name' => 'PMT Terms',
                'key_code'   => '14',
                'value_en'   => '14 Days',
                'value_no'   => '14 Dager',
            ),
            array(
                'group_code' => '009',
                'group_name' => 'PMT Terms',
                'key_code'   => '20',
                'value_en'   => '20 Days',
                'value_no'   => '20 Dager',
            ),
            array(
                'group_code' => '009',
                'group_name' => 'PMT Terms',
                'key_code'   => '30',
                'value_en'   => '30 Days',
                'value_no'   => '30 Dager',
            ),
            array(
                'group_code' => '009',
                'group_name' => 'PMT Terms',
                'key_code'   => '45',
                'value_en'   => '45 Days',
                'value_no'   => '45 Dager',
            ),
            array(
                'group_code' => '009',
                'group_name' => 'PMT Terms',
                'key_code'   => '60',
                'value_en'   => '60 Days',
                'value_no'   => '60 Dager',
            ),

            // Units
            array(
                'group_code' => '010',
                'group_name' => 'Units',
                'key_code'   => '1',
                'value_en'   => 'Hour',
                'value_no'   => 'time',
            ),
            array(
                'group_code' => '010',
                'group_name' => 'Units',
                'key_code'   => '2',
                'value_en'   => 'Pcs',
                'value_no'   => 'stk',
            ),
            array(
                'group_code' => '010',
                'group_name' => 'Units',
                'key_code'   => '3',
                'value_en'   => 'Kg',
                'value_no'   => 'kg',
            ),

            //User Type
            array(
                'group_code' => '001',
                'group_name' => 'Usertype',
                'key_code'   => 'Admin',
                'value_en'   => 'Admin',
                'value_no'   => 'Admin',
            ),
            array(
                'group_code' => '001',
                'group_name' => 'Usertype',
                'key_code'   => 'User',
                'value_en'   => 'User',
                'value_no'   => 'Bruker',
            ),
            array(
                'group_code' => '001',
                'group_name' => 'Usertype',
                'key_code'   => 'Department Chief',
                'value_en'   => 'Department Chief',
                'value_no'   => 'Avdelingsleder',
            ),

            array(
                'group_code' => '001',
                'group_name' => 'Usertype',
                'key_code'   => 'Administrative',
                'value_en'   => 'Administrative',
                'value_no'   => 'Administrativt',
            ),

            //warehouser order status
            array(
                'group_code' => '013',
                'group_name' => 'Warehouse Order Status',
                'key_code'   => '1',
                'value_en'   => 'Draft',
                'value_no'   => 'Utkast',
            ),
            array(
                'group_code' => '013',
                'group_name' => 'Warehouse Order Status',
                'key_code'   => '2',
                'value_en'   => 'Ordered from supplier',
                'value_no'   => 'bestilling',
            ),
            array(
                'group_code' => '013',
                'group_name' => 'Warehouse Order Status',
                'key_code'   => '3',
                'value_en'   => 'In Progress',
                'value_no'   => 'Mottak',
            ),
            array(
                'group_code' => '013',
                'group_name' => 'Warehouse Order Status',
                'key_code'   => '4',
                'value_en'   => 'Partially received',
                'value_no'   => 'delvis mottatt',
            ),
            array(
                'group_code' => '013',
                'group_name' => 'Warehouse Order Status',
                'key_code'   => '5',
                'value_en'   => 'Received',
                'value_no'   => 'Mottatt',
            ),
            array(
                'group_code' => '013',
                'group_name' => 'Warehouse Order Status',
                'key_code'   => '6',
                'value_en'   => 'Archived',
                'value_no'   => 'Arkiv',
            ),
            array(
                'group_code' => '013',
                'group_name' => 'Warehouse Order Status',
                'key_code'   => '7',
                'value_en'   => 'Request',
                'value_no'   => 'Forespørsel',
            ),
            array(
                'group_code' => '013',
                'group_name' => 'Warehouse Order Status',
                'key_code'   => '8',
                'value_en'   => 'Partially picked',
                'value_no'   => 'Delvis plukket',
            ),
            array(
                'group_code' => '013',
                'group_name' => 'Warehouse Order Status',
                'key_code'   => '9',
                'value_en'   => 'Picked',
                'value_no'   => 'Plukket',
            ),

            //Order Type
            array(
                'group_code' => '014',
                'group_name' => 'Warehouse Order Type',
                'key_code'   => '1',
                'value_en'   => 'Transfer order',
                'value_no'   => 'Lageroverføring',
            ),
            array(
                'group_code' => '014',
                'group_name' => 'Warehouse Order Type',
                'key_code'   => '2',
                'value_en'   => 'Adjustment order',
                'value_no'   => 'Lagerjustering',
            ),
            array(
                'group_code' => '014',
                'group_name' => 'Warehouse Order Type',
                'key_code'   => '3',
                'value_en'   => 'Supplier order',
                'value_no'   => 'Innkjøp',
            ),
            array(
                'group_code' => '014',
                'group_name' => 'Warehouse Order Type',
                'key_code'   => '4',
                'value_en'   => 'Return',
                'value_no'   => 'Retur',
            ),
            //Currency name
            array(
                'group_code' => '015',
                'group_name' => 'Currency Name',
                'key_code'   => 'USD',
                'value_en'   => 'USD',
                'value_no'   => 'USD',
            ),
            array(
                'group_code' => '015',
                'group_name' => 'Currency Name',
                'key_code'   => 'NOK',
                'value_en'   => 'NOK',
                'value_no'   => 'NOK',
            ),
            array(
                'group_code' => '015',
                'group_name' => 'Currency Name',
                'key_code'   => 'SEK',
                'value_en'   => 'SEK',
                'value_no'   => 'SEK',
            ),
            array(
                'group_code' => '015',
                'group_name' => 'Currency Name',
                'key_code'   => 'EUR',
                'value_en'   => 'EUR',
                'value_no'   => 'EUR',
            ),
            array(
                'group_code' => '015',
                'group_name' => 'Currency Name',
                'key_code'   => 'DKK',
                'value_en'   => 'DKK',
                'value_no'   => 'DKK',
            ),
            array(
                'group_code' => '015',
                'group_name' => 'Currency Name',
                'key_code'   => 'GBP',
                'value_en'   => 'GBP',
                'value_no'   => 'GBP',
            ),

            //Customer Address type
            array(
                'group_code' => '017',
                'group_name' => 'Customer Address Type',
                'key_code'   => '1',
                'value_en'   => 'Department',
                'value_no'   => 'Avdeling',
            ),
            array(
                'group_code' => '017',
                'group_name' => 'Customer Address Type',
                'key_code'   => '2',
                'value_en'   => 'Invoice',
                'value_no'   => 'Faktura',
            ),
            array(
                'group_code' => '017',
                'group_name' => 'Customer Address Type',
                'key_code'   => '3',
                'value_en'   => 'Shipping',
                'value_no'   => 'Forsendelser',
            ),

            //ccsheet status
            array(
                'group_code' => '018',
                'group_name' => 'CC Status',
                'key_code'   => '1',
                'value_en'   => 'On going',
                'value_no'   => 'Under arbeid',
            ),
            array(
                'group_code' => '018',
                'group_name' => 'CC Status',
                'key_code'   => '5',
                'value_en'   => 'Completed',
                'value_no'   => 'Fullført',
            ),
            // Offer Status
            array(
                'group_code' => '020',
                'group_name' => 'Offer Status',
                'key_code'   => '1',
                'value_en'   => 'New',
                'value_no'   => 'Nytt',
            ),
            array(
                'group_code' => '020',
                'group_name' => 'Offer Status',
                'key_code'   => '2',
                'value_en'   => 'Sent',
                'value_no'   => 'Sendt',
            ),
            array(
                'group_code' => '020',
                'group_name' => 'Offer Status',
                'key_code'   => '3',
                'value_en'   => 'Accepted',
                'value_no'   => 'Akseptert',
            ),
            array(
                'group_code' => '020',
                'group_name' => 'Offer Status',
                'key_code'   => '4',
                'value_en'   => 'Not Accepted',
                'value_no'   => 'Ikke akseptert',
            ),
        ));
    }
}
