@php
    $languages = (isset($logged_user_data['languages']))?$logged_user_data['languages']:[];
    $selected_language = (isset($logged_user_data['selected_language']))?$logged_user_data['selected_language']:'en - English'; 
    $fixed_footer = (isset($fixed_footer) && $fixed_footer) ? "fixed-bottom" : "";
    $year = date("Y").' - '.date("Y", strtotime("+1 year"));
    $company = config('app.company');
    $version = 'v5.4';
@endphp

<footercomponent :languages="{{ json_encode($languages) }}" :selected_language="{{ json_encode($selected_language) }}" :fixed_footer="{{ json_encode($fixed_footer) }}" :year="{{ json_encode($year) }}" :company="{{ json_encode($company) }}" :version="{{ json_encode($version) }}"></footercomponent>