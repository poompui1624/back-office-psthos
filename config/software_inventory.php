<?php

return [

    /*
    |--------------------------------------------------------------------------
    | System components
    |--------------------------------------------------------------------------
    |
    | Package names matching any of these are flagged is_component and hidden
    | from the inventory by default. They are real installs, so nothing is
    | discarded — but a redistributable is a dependency of something else, not
    | a piece of software anyone deployed on purpose, and on a measured machine
    | they were 40 of 135 entries.
    |
    | Matched case-insensitively against the package name.
    |
    */

    'component_patterns' => [
        // Runtimes and redistributables
        'Redistributable',
        'Microsoft Visual C\+\+',
        'Microsoft \.NET',
        '\.NET (Framework|Core|Runtime|Host|Targeting Pack)',
        'Visual Studio.*(Tools|Runtime|Redistributable)',
        'Java.*(Runtime|Update)',
        'Microsoft Edge (WebView|Update)',
        'Windows Desktop Runtime',
        'ASP\.NET Core',

        // Updates and patches, which describe maintenance rather than software
        '^Update for ',
        '^Security Update',
        '^Hotfix',
        'Service Pack',
        'Language Pack',
        'Cumulative Update',

        // Drivers and hardware support
        'Driver(s)?$',
        ' Driver ',
        'Realtek.*Audio',
        'Intel\(R\).*(Driver|Chipset|Management|Graphics)',
        'NVIDIA.*(Driver|PhysX|Graphics)',
        'AMD.*(Driver|Chipset)',
        'Chipset Device Software',

        // SDKs and developer plumbing
        'Windows (SDK|Software Development Kit|Driver Kit)',
        'Debugging Tools',
        'Universal CRT',
        'MSI Development Tools',
    ],

];
