<?php

namespace App\Services\Helper;

// use Modules\Admin\Repositories\LinkCategoryRepository,
//     Modules\Admin\Repositories\LinksRepository,
//     Modules\Admin\Models\Links,
//     Modules\Admin\Models\LinkCategory,
use   Auth;
//     Illuminate\Support\Facades\Route;

class MenuHelper
{

    /**
     * fetch links user is allowed to
     * @return String
     */
    public static function getSideBarLinks()
    {
        $user = Auth::user()::with('roles')->get();
        
        print_r($user); exit;
        $sidebarLinks = ['sfsf'];
        // $linksCategoryRepository = new LinkCategoryRepository(new LinkCategory);
        // $sidebarLinks = $linksCategoryRepository->getSidebarLinks($user_type_id);

        return $sidebarLinks;
    }

    // /**
    //  * get route of page
    //  * @return Links Array
    //  */
    // public static function getRouteByPage()
    // {
    //     $linkDetails = ['page_header' => '', 'page_text' => '', 'category_description' => ''];
    //     $currentRoute = Route::currentRouteName();
    //     if (!empty($currentRoute)) {
    //         $linksRepository = new LinksRepository(new Links);
    //         $linksData = $linksRepository->getLinksDataByRoute($currentRoute);
    //         $linkDetails = ['page_header' => $linksData['page_header'], 'link_name' => $linksData['link_name'], 'link_icon' => $linksData['link_icon'], 'link_url' => $linksData['link_url'], 'page_text' => $linksData['page_text'], 'category_name' => $linksData['link_category']['category'], 'category_description' => $linksData['link_category']['header_text'], 'pagination' => $linksData['pagination']];
    //     }

    //     return $linkDetails;
    // }

    // /**
    //  * get selected page link icon
    //  * @return Links String
    //  */
    // public static function getSelectedPageLinkIcon()
    // {
    //     $linkDetailsArray = self::getRouteByPage();
    //     $linkIcon = $linkDetailsArray['link_icon'];
    //     return $linkIcon;
    // }
}
