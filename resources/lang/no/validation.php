<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attribute må være godkjent.',
    'active_url'           => ':attribute er ikke en gyldig link.',
    'after'                => ':attribute må være dato etter :date.',
    'alpha'                => ':attribute kan bare ha bokstaver.',
    'alpha_dash'           => ':attribute kan bare ha bokstaver, tall og - .',
    'alpha_num'            => ':attribute kan bare ha bokstaver og tall.',
    'array'                => ':attribute må være en matrise.',
    'before'               => ':attribute må være dato før :date.',
    'between'              => [
        'numeric' => ':attribute må være mellom :min og :max.',
        'file'    => ':attribute må være mellom :min og :max kb.',
        'string'  => ':attribute må være mellom :min og :max tegn.',
        'array'   => ':attribute må ha mellom :min og :max valg.',
    ],
    'boolean'              => ':attribute må være SANT eller USANT.',
    'confirmed'            => 'The :attribute confirmation does not match.',
    'date'                 => ':attribute er ikke gyldig dato.',
    'date_format'          => ':attribute skal være :format.',
    'different'            => 'The :attribute and :other must be different.',
    'digits'               => 'The :attribute must be :digits digits.',
    'digits_between'       => 'The :attribute must be between :min and :max digits.',
    'dimensions'           => 'The :attribute has invalid image dimensions.',
    'distinct'             => 'The :attribute field has a duplicate value.',
    'email'                => ':attribute må være gyldig epost.',
    'exists'               => 'The selected :attribute is invalid.',
    'filled'               => 'The :attribute field is required.',
    'image'                => 'The :attribute must be an image.',
    'in'                   => 'Valgt :attribute er ugyldig.',
    'in_array'             => 'The :attribute field does not exist in :other.',
    'integer'              => 'The :attribute must be an integer.',
    'ip'                   => 'The :attribute must be a valid IP address.',
    'json'                 => 'The :attribute must be a valid JSON string.',
    'max'                  => [
        'numeric' => ':attribute kan ikke være større enn :max.',
        'file'    => ':attribute kan ikke være større enn :max kilobytes.',
        'string'  => ':attribute kan ikke være større enn :max characters.',
        'array'   => ':attribute kan ikke være større enn :max items.',
    ],
    'mimes'                => ':attribute må være fil av type: :values.',
    'min'                  => [
        'numeric' => 'The :attribute must be at least :min.',
        'file'    => 'The :attribute must be at least :min kilobytes.',
        'string'  => 'The :attribute must be at least :min characters.',
        'array'   => 'The :attribute must have at least :min items.',
    ],
    'not_in'               => 'The selected :attribute is invalid.',
    'numeric'              => 'The :attribute must be a number.',
    'present'              => 'The :attribute field must be present.',
    'regex'                => 'The :attribute format is invalid.',
    'required'             => 'The :attribute field is required.',
    'required_if'          => 'The :attribute field is required when :other is :value.',
    'required_unless'      => 'The :attribute field is required unless :other is in :values.',
    'required_with'        => 'The :attribute field is required when :values is present.',
    'required_with_all'    => 'The :attribute field is required when :values is present.',
    'required_without'     => 'The :attribute field is required when :values is not present.',
    'required_without_all' => 'The :attribute field is required when none of :values are present.',
    'same'                 => 'The :attribute and :other must match.',
    'size'                 => [
        'numeric' => ':attribute må være :size.',
        'file'    => ':attribute må være :size kb.',
        'string'  => ':attribute må være :size tegn.',
        'array'   => ':attribute må ha :size valg.',
    ],
    'string'               => 'The :attribute must be a string.',
    'timezone'             => 'The :attribute must be a valid zone.',
    'unique'               => 'The :attribute has already been taken.',
    'url'                  => 'The :attribute format is invalid.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
