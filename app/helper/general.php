<?php
function checkInputPostageCharges($custServiceCharges)
{
    $discountedPostageLt5 = 0;
    $discountedPostageLt9 = 0;
    $discountedPostageLt13 = 0;
    $discountedPostageGte13 = 0;
    
    $discountedPostagelbs1_1_99 = 0;
    $discountedPostagelbs1_1_2 = 0;
    $discountedPostagelbs2_1_3 = 0;
    $discountedPostagelbs3_1_4 = 0;

    if (isset($custServiceCharges)) {
        if ($custServiceCharges->discounted_default_postage_charges == 0) {
            $discountedPostageLt5 = $custServiceCharges->postage_cost_lt5;
            $discountedPostageLt9 = $custServiceCharges->postage_cost_lt9;
            $discountedPostageLt13 = $custServiceCharges->postage_cost_lt13;
            $discountedPostageGte13 = $custServiceCharges->postage_cost_gte13;

            $discountedPostagelbs1_1_99 = $custServiceCharges->lbs1_1_99;
            $discountedPostagelbs1_1_2 = $custServiceCharges->lbs1_1_2;
            $discountedPostagelbs2_1_3 = $custServiceCharges->lbs2_1_3;
            $discountedPostagelbs3_1_4 = $custServiceCharges->lbs3_1_4;
        } else {
            $discountedPostageLt5 = $custServiceCharges->discounted_postage_cost_lt5;
            $discountedPostageLt9 = $custServiceCharges->discounted_postage_cost_lt9;
            $discountedPostageLt13 = $custServiceCharges->discounted_postage_cost_lt13;
            $discountedPostageGte13 = $custServiceCharges->discounted_postage_cost_gte13;

            $discountedPostagelbs1_1_99 = $custServiceCharges->discounted_lbs1_1_99;
            $discountedPostagelbs1_1_2 = $custServiceCharges->discounted_lbs1_1_2;
            $discountedPostagelbs2_1_3 = $custServiceCharges->discounted_lbs2_1_3;
            $discountedPostagelbs3_1_4 = $custServiceCharges->discounted_lbs3_1_4;
        }
    }
    return [
        'postage_costlt5' => $discountedPostageLt5,
        'postage_costlt9' => $discountedPostageLt9,
        'postage_costlt13' => $discountedPostageLt13,
        'postage_costgte13' => $discountedPostageGte13,

        'postage_costlbs1_1_99' => $discountedPostagelbs1_1_99,
        'postage_costlbs1_1_2' => $discountedPostagelbs1_1_2,
        'postage_costlbs2_1_3' => $discountedPostagelbs2_1_3,
        'postage_costlbs3_1_4' => $discountedPostagelbs3_1_4,
    ];
}
