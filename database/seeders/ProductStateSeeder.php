<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class ProductStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $setting = Setting::where('id', 1)->first();
        if ($setting->state == NULL) {
            $setting->state = '{
                "time": "1642103825786",
                "order": [
                    [
                        "2",
                        "asc"
                    ]
                ],
                "start": "0",
                "length": "10",
                "search": {
                    "regex": "false",
                    "smart": "true",
                    "search": null,
                    "caseInsensitive": "true"
                },
                "columns": [
                    {
                        "search": {
                            "regex": "false",
                            "smart": "true",
                            "search": null,
                            "caseInsensitive": "true"
                        },
                        "visible": "true"
                    },
                    {
                        "search": {
                            "regex": "false",
                            "smart": "true",
                            "search": null,
                            "caseInsensitive": "true"
                        },
                        "visible": "true"
                    },
                    {
                        "search": {
                            "regex": "false",
                            "smart": "true",
                            "search": null,
                            "caseInsensitive": "true"
                        },
                        "visible": "true"
                    },
                    {
                        "search": {
                            "regex": "false",
                            "smart": "true",
                            "search": null,
                            "caseInsensitive": "true"
                        },
                        "visible": "true"
                    },
                    {
                        "search": {
                            "regex": "false",
                            "smart": "true",
                            "search": null,
                            "caseInsensitive": "true"
                        },
                        "visible": "true"
                    },
                    {
                        "search": {
                            "regex": "false",
                            "smart": "true",
                            "search": null,
                            "caseInsensitive": "true"
                        },
                        "visible": "true"
                    },
                    {
                        "search": {
                            "regex": "false",
                            "smart": "true",
                            "search": null,
                            "caseInsensitive": "true"
                        },
                        "visible": "true"
                    },
                    {
                        "search": {
                            "regex": "false",
                            "smart": "true",
                            "search": null,
                            "caseInsensitive": "true"
                        },
                        "visible": "true"
                    },
                    {
                        "search": {
                            "regex": "false",
                            "smart": "true",
                            "search": null,
                            "caseInsensitive": "true"
                        },
                        "visible": "true"
                    },
                    {
                        "search": {
                            "regex": "false",
                            "smart": "true",
                            "search": null,
                            "caseInsensitive": "true"
                        },
                        "visible": "true"
                    },
                    {
                        "search": {
                            "regex": "false",
                            "smart": "true",
                            "search": null,
                            "caseInsensitive": "true"
                        },
                        "visible": "true"
                    },
                    {
                        "search": {
                            "regex": "false",
                            "smart": "true",
                            "search": null,
                            "caseInsensitive": "true"
                        },
                        "visible": "true"
                    },
                    {
                        "search": {
                            "regex": "false",
                            "smart": "true",
                            "search": null,
                            "caseInsensitive": "true"
                        },
                        "visible": "true"
                    }
                ]
            }';
            $setting->save();
        }
    }
}
