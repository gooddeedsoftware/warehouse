<?php

namespace App\Models;

use App\Helpers\GanticHelper;
use App\Models\Customer;
use App\Models\Department;
use App\Models\UniAccounts;
use App\Models\UNICustomers;
use App\Models\UniRefreshToken;
use App\Models\UniSellers;
use Illuminate\Database\Eloquent\Model;
use Session;

class UniIntegration extends Model
{
    /**
     * [createAccessTokenFromCode description]
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public static function createAccessTokenFromCode($data)
    {
        $url                        = config('app.UNI_AUTH_URL') . "/connect/token";
        $post_fields                = "grant_type=authorization_code&code=" . $data['code'] . "&client_id=" . config('app.UNI_CLIENT_ID') . "&client_secret=" . config('app.UNI_CLIENT_SECRET') . "&redirect_uri=" . config('app.UNI_REDIRECT_URI') . "";
        $headers                    = ["content-type: application/x-www-form-urlencoded"];
        $service_response           = curlPostRequest($url, $post_fields, $headers);
        $save_data                  = array();
        $save_data['refresh_token'] = $service_response->refresh_token;
        UniRefreshToken::create($save_data);
    }

    /**
     * [createAccessTokenFromRefreshToken description]
     * @return [type] [description]
     */
    public static function createAccessTokenFromRefreshToken()
    {
        $refresh_token         = UniRefreshToken::select('refresh_token', 'id')->first();
        $post_fields           = "grant_type=refresh_token&client_id=" . config('app.UNI_CLIENT_ID') . "&client_secret=" . config('app.UNI_CLIENT_SECRET') . "&redirect_uri=" . config('app.UNI_REDIRECT_URI') . "&refresh_token=" . $refresh_token->refresh_token . "";
        $url                   = config('app.UNI_AUTH_URL') . "/connect/token";
        $headers               = ["content-type: application/x-www-form-urlencoded"];
        $access_token_response = curlPostRequest($url, $post_fields, $headers);
        if (@$access_token_response && @$access_token_response->access_token) {
            UniRefreshToken::whereId($refresh_token->id)->update(['refresh_token' => @$access_token_response->refresh_token]);
            return $access_token_response->access_token;
        }
        return null;
    }

    /**
     * [createOrderInUNi description]
     * @param  [type] $order_details [description]
     * @return [type]                [description]
     */
    public static function createOrderInUNi($order_details)
    {
        if (!$order_details->uni_status) {
            $headers = array(
                "authorization: Bearer " . UniIntegration::createAccessTokenFromRefreshToken(),
                "CompanyKey:" . config('app.UNI_COMPANY_KEY'),
                "content-type: application/json",
            );
            $department_id = null;
            if (@$order_details->order_department && @$order_details->order_department[0]) {
                $department_result = Department::whereId($order_details->order_department[0]->department_id)->first();
                $department_id     = @$department_result->uni_department;
            }
            $pmt_term                = UniIntegration::getPmtTerm(@$order_details->pmt_term);
            $customer_details        = Customer::where('id', $order_details->customer_id)->first();
            $address_details         = UniIntegration::getAddressDetails($order_details, $customer_details);
            $order_by_person_details = Contact::where('id', $order_details->ordered_by)->first();
            $post_fields             = '{
                            "Accrual": null,
                            "AccrualID": 0,
                            "Comment": "' . $order_details->comments . '",
                            "CreatedAt": null,
                            "CreatedBy": null,
                            "CreditDays": 0,
                            "CurrencyCode": null,
                            "CurrencyCodeID": 1,
                            "CurrencyExchangeRate": 1,
                            "CustomValues": null,
                            "CustomerID": ' . $customer_details->uni_id . ',
                            "CustomerName": "' . $customer_details->name . '",
                            "CustomerOrgNumber": null,
                            "CustomerPerson": null,
                            "DefaultDimensionsID": null,
                            "DefaultSeller": null,
                            "Deleted": false,
                            "DeliveryDate": "' . $order_details->date_completed . '",
                            "DeliveryMethod": null,
                            "DeliveryName": null,
                            "DeliveryTerm": null,
                            "DeliveryTerms": null,
                            "EmailAddress":  "' . $customer_details->email . '",
                            "EntityType": "Models.Sales.CustomerOrder",
                            "FreeTxt": "' . $order_details->order_invoice_comments . '",
                            "ID": 0,
                            "Items": [],
                            "OrderDate": "' . date('Y-m-d') . '",
                            "OurReference": "' . $order_details->order_number . '",
                            "Requisition": "' . $order_details->project_number . '",
                            "YourReference": "' . @$order_by_person_details->name . '",
                            "DefaultSellerID": ' . @$order_details->responsibleUser->uni_seller . ',
                            "sellers": [
                                {
                                    "SellerID":' . @$order_details->responsibleUser->uni_seller . ',
                                    "Percent": 100
                                }
                            ],
                            "DefaultDimensions": {
                                "DepartmentID": ' . $department_id . '
                            },
                            "PaymentTermsID": ' . $pmt_term . ',
                            "DeliveryTermsID": 1,
                            "ReadyToInvoice": true,
                            "ShippingAddressLine1": "' . @$order_details->deliveraddress1 . '",
                            "ShippingAddressLine2": "' . @$order_details->deliveraddress2 . '",
                            "ShippingPostalCode": "' . @$order_details->deliveraddress_zip . '",
                            "ShippingCity": "' . @$order_details->deliveraddress_city . '",
                            "InvoiceAddressLine1":"' . @$order_details->deliveraddress1 . '",
                            "InvoiceAddressLine2": "' . @$order_details->deliveraddress2 . '",
                            "InvoicePostalCode": "' . @$order_details->deliveraddress_zip . '",
                            "InvoiceCity": "' . @$order_details->deliveraddress_city . '",
                        }';
            $decoded_response = curlPostRequest(config('app.UNI_BASE_URL') . "/orders", $post_fields, $headers);
            if (@$decoded_response && @$decoded_response->ID) {
                Order::whereId($order_details->id)->update(['uni_status' => $decoded_response->ID]);
                return $decoded_response->ID;
            } else {
                $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Order_no ' . @$order_details->order_number . ' - ' . json_encode(@$decoded_response) . ']';
                register_error_log($error_message, 'uni.log');
            }
            return false;
        } else {
            return $order_details->uni_status;
        }
    }

    /**
     * [getAddressDetails description]
     * @return [type] [description]
     */
    public static function getAddressDetails($order_details, $customer_details)
    {

        if (!$customer_details->uni_id) {
            return null;
        }
        $headers = array(
            "authorization: Bearer " . UniIntegration::createAccessTokenFromRefreshToken(),
            "CompanyKey:" . config('app.UNI_COMPANY_KEY'),
            "content-type: application/json",
        );
        $decoded_response = curlGetRequest(config('app.UNI_BASE_URL') . "/customers/" . $customer_details->uni_id . "?hateoas=false&expand=Info.Addresses", $headers);
        if ($decoded_response) {
            $address          = collect($decoded_response);
            $delivery_address = collect($address['Info']->Addresses);
            $delivery_result  = $delivery_address->where('AddressLine1', $order_details->deliveraddress1)->where('PostalCode', $order_details->deliveraddress_zip)->first();
            if (!$delivery_result) {
                UniIntegration::createCustomerAddressInUni($decoded_response->BusinessRelationID, $order_details, $customer_details->uni_id);
            }
            return true;
        }
    }

    /**
     * [createCustomerAddressInUni description]
     * @param  [type] $address1 [description]
     * @param  [type] $zip      [description]
     * @return [type]           [description]
     */
    public static function createCustomerAddressInUni($businessRelationID, $order_details, $uni_id)
    {

        $headers = array(
            "authorization: Bearer " . UniIntegration::createAccessTokenFromRefreshToken(),
            "CompanyKey:" . config('app.UNI_COMPANY_KEY'),
            "content-type: application/json",
        );
        $post_fields = '{
                            "ID": "' . @$uni_id . '",
                            "Info": {
                                "ID": "' . $businessRelationID . '",
                                "Addresses": [
                                    {
                                        "City":  "' . @$order_details->deliveraddress_city . '",
                                        "PostalCode": "' . @$order_details->deliveraddress_zip . '",
                                        "AddressLine2": "' . @$order_details->deliveraddress2 . '",
                                        "AddressLine1": "' . @$order_details->deliveraddress1 . '",
                                        "_createguid":"' . GanticHelper::gen_uuid() . '"
                                    }
                                ]
                            }
                        }';
        $decoded_response = curlPutRequest(config('app.UNI_BASE_URL') . "/customers/" . $uni_id, $post_fields, $headers);
        if (@$decoded_response && @$decoded_response->ID) {
            return true;
        } else {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Order_no ' . @$order_details->order_number . ' - ' . json_encode(@$decoded_response) . ']';
            register_error_log($error_message, 'uni.log');
        }
        return false;

    }

    /**
     * [getPmtTerm description]
     * @param  [type] $term [description]
     * @return [type]       [description]
     */
    public static function getPmtTerm($term)
    {
        if (!$term) {
            return null;
        }
        $headers = array(
            "authorization: Bearer " . UniIntegration::createAccessTokenFromRefreshToken(),
            "CompanyKey:" . config('app.UNI_COMPANY_KEY'),
            "content-type: application/json",
        );
        $decoded_response = curlGetRequest(config('app.UNI_BASE_URL') . "/terms", $headers);
        if ($decoded_response) {
            $uni_terms    = collect($decoded_response);
            $payment_term = $uni_terms->where('TermsType', 1)->where('CreditDays', $term)->first();
            return @$payment_term->ID;
        }
    }

    /**
     * [sendOrderItemsToUNI description]
     * @param  [type] $material_id  [description]
     * @param  [type] $units        [description]
     * @param  [type] $uni_order_id [description]
     * @return [type]               [description]
     */
    public static function sendOrderItemsToUNI($material_id, $units, $uni_order_id)
    {
        $material_detail = OrderMaterial::whereId($material_id)->with('product', 'product.acc_plan')->first();
        $product_uni_id  = null;
        if ($material_detail->is_text != 1) {
            $product_uni_id = @$material_detail->product->uni_id ? $material_detail->product->uni_id : self::createProductInUni($material_detail->product, $units);
        }
        if ($product_uni_id || $material_detail->is_text == 1) {
            $invoice_quantity = 0;
            $unit             = 0;
            $sale_price       = 0;
            $discount         = 0;
            $vat              = 0;
            $discount_price   = 0;
            $cost_price       = 0;
            $description      = $material_detail->product_text;
            if ($material_detail->is_text != 1) {
                $description      = @$material_detail->product_description ? $material_detail->product_description : @$material_detail->product->description;
                $invoice_quantity = @$material_detail->quantity;
                $language         = Session::get('language') ? Session::get('language') : 'no';
                $units            = DropdownHelper::where("groupcode", "=", "010")->where('language', '=', $language)->orderby("keycode", "asc")->pluck("label", "keycode");
                $unit             = @$units[@$material_detail->unit];
                $sale_price       = @$material_detail->offer_sale_price;
                $discount         = @$material_detail->discount;
                $cost_price       = @$material_detail->cost_price;
                $vat              = @$material_detail->product->tax;
                $discount_price   = ($invoice_quantity * $sale_price);
                if ($discount > 0) {
                    $discount_price = ($invoice_quantity * $sale_price) - ($invoice_quantity * $sale_price * $discount / 100);
                }
            }
            $headers = array(
                "authorization: Bearer " . UniIntegration::createAccessTokenFromRefreshToken(),
                "CompanyKey:" . config('app.UNI_COMPANY_KEY'),
                "content-type: application/json",
            );
            $post_fields = '{
                    "CustomValues": {},
                    "CustomerOrderID": ' . $uni_order_id . ',
                    "SumVat": ' . $vat . ',
                    "Deleted": false,
                    "SumVatCurrency": ' . $discount_price . ',
                    "SortIndex": 6,
                    "SumTotalIncVat": ' . $discount_price . ',
                    "ID": 0,
                    "DiscountPercent": ' . $discount . ',
                    "CreatedBy": "",
                    "AccountID": ' . @$material_detail->product->acc_plan->uni_id . ',
                    "PriceExVat": ' . $sale_price . ',
                    "CalculateGrossPriceBasedOnNetPrice": false,
                    "SumTotalExVatCurrency": ' . $discount_price . ',
                    "CurrencyExchangeRate": 1.00000,
                    "NumberOfItems": ' . $invoice_quantity . ',
                    "StatusCode": 41102,
                    "ReadyToInvoice": true,
                    "PriceSetByUser": false,
                    "PriceIncVat": ' . $sale_price . ',
                    "Unit": "' . $unit . '",
                    "ItemText": "' . $description . '",
                    "Discount": 0,
                    "VatPercent": 25.0000,
                    "PriceExVatCurrency": ' . $sale_price . ',
                    "CostPrice": ' . $cost_price . ',
                    "SumTotalExVat": ' . $discount_price . ',
                    "VatTypeID": 11,
                    "ProductID": ' . $product_uni_id . ',
                    "SumTotalIncVatCurrency": ' . $discount_price . ',
                    "CurrencyCodeID": 1
                }';
            $decoded_response = curlPostRequest(config('app.UNI_BASE_URL') . "/orderitems", $post_fields, $headers);
            if ($decoded_response && @$decoded_response->ID) {
                return $decoded_response->ID;
            } else {
                $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Order_no ' . @$order_details->order_number . ' - ' . json_encode(@$decoded_response) . ']';
                register_error_log($error_message, 'uni.log');
            }
        }
        return false;
    }

    /**
     * [createProductInUni description]
     * @param  [type] $product_details [description]
     * @param  [type] $units           [description]
     * @return [type]                  [description]
     */
    public static function createProductInUni($product_details, $units)
    {
        $headers = array(
            "authorization: Bearer " . UniIntegration::createAccessTokenFromRefreshToken(),
            "CompanyKey:" . config('app.UNI_COMPANY_KEY'),
            "content-type: application/json",
        );
        $post_fields = '{
                        "PartName": "' . $product_details->product_number . '",
                        "Name": "' . $product_details->description . '",
                        "CostPrice": "' . $product_details->cost_price . '",
                        "PriceIncVat": "' . $product_details->sale_price_with_vat . '",
                        "PriceExVat": "' . $product_details->sale_price . '",
                        "Unit": "' . @$units[$product_details->unit] . '",
                        "ID": 0,
                        "Deleted": false
                    }';
        $decoded_response = curlPostRequest(config('app.UNI_BASE_URL') . "/products", $post_fields, $headers);
        if ($decoded_response && @$decoded_response->ID) {
            Product::whereId($product_details->id)->update(['uni_id' => $decoded_response->ID]);
            return $decoded_response->ID;
        }
        return false;
    }

    /**
     * [fetchCompanyDetails description]
     * @return [type] [description]
     */
    public static function fetchCompanyDetails()
    {
        $url  = config('app.UNI_APP_URL') . "/api/init/companies";
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_PORT           => "443",
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => "GET",
            CURLOPT_HTTPHEADER     => ["Authorization: Bearer " . UniIntegration::createAccessTokenFromRefreshToken()],
        ]);
        $response = curl_exec($curl);
        $err      = curl_error($curl);
        var_dump($err);
        echo "<br />";
        dd($response);
    }

    /**
     * [fetchUniAccounts description]
     * @return [type] [description]
     */
    public static function fetchUniAccounts()
    {
        try {
            if (!checkUNITokenExists()) {
                return 2;
            }
            $headers = array(
                "authorization: Bearer " . UniIntegration::createAccessTokenFromRefreshToken(),
                "CompanyKey:" . config('app.UNI_COMPANY_KEY'),
                "content-type: application/json",
            );
            $decoded_response = curlGetRequest(config('app.UNI_BASE_URL') . "/accounts", $headers);
            if ($decoded_response) {
                $uni_account_results = collect($decoded_response);
                foreach ($uni_account_results as $key => $value) {
                    $uni_account_array                 = [];
                    $uni_account_array['uni_id']       = $value->ID;
                    $uni_account_array['account_no']   = $value->AccountNumber;
                    $uni_account_array['account_name'] = $value->AccountName;
                    $uni_account                       = UniAccounts::where('uni_id', $value->ID)->first();
                    if ($uni_account) {
                        $uni_account->fill($uni_account_array);
                        $uni_account->update();
                    } else {
                        UniAccounts::create($uni_account_array);
                    }
                }

            }
            return 1;
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            register_error_log($error_message, 'accplan.log');
            return 0;
        }

    }

    /**
     * [fetchSellers description]
     * @return [type] [description]
     */
    public static function fetchSellers()
    {
        try {
            if (!checkUNITokenExists()) {
                return 2;
            }
            $headers = array(
                "authorization: Bearer " . UniIntegration::createAccessTokenFromRefreshToken(),
                "CompanyKey:" . config('app.UNI_COMPANY_KEY'),
                "content-type: application/json",
            );
            $decoded_response = curlGetRequest(config('app.UNI_BASE_URL') . "/sellers", $headers);
            if ($decoded_response) {
                $uni_seller_results = collect($decoded_response);
                foreach ($uni_seller_results as $key => $value) {
                    $uni_seller_array           = [];
                    $uni_seller_array['uni_id'] = $value->ID;
                    $uni_seller_array['name']   = $value->Name;
                    $uni_seller                 = UniSellers::where('uni_id', $value->ID)->first();
                    if ($uni_seller) {
                        $uni_seller->fill($uni_seller_array);
                        $uni_seller->update();
                    } else {
                        UniSellers::create($uni_seller_array);
                    }
                }

            }
            return 1;
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            register_error_log($error_message, 'user.log');
            return 0;
        }

    }

    /**
     * [fetchUniDepartments description]
     * @return [type] [description]
     */
    public static function fetchUniDepartments()
    {
        try {
            if (!checkUNITokenExists()) {
                return 2;
            }
            $headers = array(
                "authorization: Bearer " . UniIntegration::createAccessTokenFromRefreshToken(),
                "CompanyKey:" . config('app.UNI_COMPANY_KEY'),
                "content-type: application/json",
            );
            $decoded_response = curlGetRequest(config('app.UNI_BASE_URL') . "/departments", $headers);
            if ($decoded_response) {
                $uni_department_results = collect($decoded_response);
                foreach ($uni_department_results as $key => $value) {
                    $uni_department_array           = [];
                    $uni_department_array['uni_id'] = $value->ID;
                    $uni_department_array['name']   = $value->Name;
                    $uni_department                 = UniDepartment::where('uni_id', $value->ID)->first();
                    if ($uni_department) {
                        $uni_department->fill($uni_department_array);
                        $uni_department->update();
                    } else {
                        UniDepartment::create($uni_department_array);
                    }
                }

            }
            return 1;
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            register_error_log($error_message, 'department.log');
            return 0;
        }

    }

    /**
     * [fetchUniDepartments description]
     * @return [type] [description]
     */
    public static function fetchUniCustomers()
    {
        try {
            if (!checkUNITokenExists()) {
                return 2;
            }
            $headers = array(
                "authorization: Bearer " . UniIntegration::createAccessTokenFromRefreshToken(),
                "CompanyKey:" . config('app.UNI_COMPANY_KEY'),
                "content-type: application/json",
            );

            $decoded_response = curlGetRequest(config('app.UNI_BASE_URL') . "/customers?expand=Info", $headers);
            if ($decoded_response) {
                $uni_customer_results = collect($decoded_response);
                foreach ($uni_customer_results as $key => $value) {
                    $uni_customer_array                    = [];
                    $uni_customer_array['uni_id']          = $value->ID;
                    $uni_customer_array['name']            = @$value->Info->Name;
                    $uni_customer_array['org_number']      = $value->OrgNumber;
                    $uni_customer_array['customer_number'] = $value->CustomerNumber;
                    $uni_customer                          = UNICustomers::where('uni_id', $value->ID)->first();
                    if ($uni_customer) {
                        $uni_customer->fill($uni_customer_array);
                        $uni_customer->update();
                    } else {
                        UNICustomers::create($uni_customer_array);
                    }
                }
            }
            return 1;
        } catch (\Exception $e) {
            $error_message = 'Date: ' . date("Y-m-d H:i:s") . ' [Line ' . $e->getLine() . ' - ' . $e->getMessage() . ' - ' . $e->getFile() . ']';
            register_error_log($error_message, 'customer.log');
            return 0;
        }
    }

}
