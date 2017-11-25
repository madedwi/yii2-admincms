<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use admin\widgets\Icon;

// get active url;
$homeUrl    = Url::home() . 'administrator/';
$currentUrl = Url::current();
$currentUrl = str_replace($homeUrl, '', $currentUrl);
$currentUrl = explode('/', $currentUrl);
$GLOBALS['mainUrl'] = isset($currentUrl[0])? $currentUrl[0] : '';
$GLOBALS['subUrl']  = isset($currentUrl[1])? $currentUrl[1] : '';
$seconSub = isset($currentUrl[2])? $currentUrl[2] : '';

$cms = Yii::$app->params['registeredAdminMenus'];

function renderMenu($menu) {
    $active = ($GLOBALS['mainUrl'] == $menu['activeIf']['mainUrl'] && in_array($GLOBALS['subUrl'], $menu['activeIf']['subUrl']));
    return Html::tag('li', Html::a(Html::tag('span', $menu['label'], ['class'=>'menu-text '.$menu['class']]), $menu['url']), ['class'=>$active? 'active' : '']);
}
?>



<div class="page-sidebar" id="sidebar">
    <!-- Page Sidebar Header-->
    <div class="sidebar-header-wrapper">
        <?= Icon::fa('home');  ?>
        <input type="text" class="searchinput" />
        <i class="searchicon fa fa-search"></i>
        <div class="searchhelper">Search Reports, Charts, Emails or Notifications</div>
    </div>
    <!-- /Page Sidebar Header -->
    <!-- Sidebar Menu -->
    <ul class="nav sidebar-menu">
        <!--Dashboard-->
        <li>
            <?= Html::a(Icon::glyph('home'). Html::tag('span', '&nbsp; Dashboard', ['class'=>'menu-text']), ['/administrator']) ?>
        </li>
        <li class="<?= ($GLOBALS['mainUrl'] =='content') ? 'open' : ''?>">
            <?= Html::a(Icon::glyph('th'). Html::tag('span', '&nbsp; General Content', ['class'=>'menu-text']), 'javascript:', ['class'=>'menu-dropdown']) ?>
            <?php
            $menus = [
                [
                    'label' => 'Page',
                    'class' => 'page',
                    'url' => ['/administrator/page/'],
                    'activeIf' => [
                        'mainUrl' => 'content',
                        'subUrl' => ['page']
                    ],
                ],
                [
                    'label' => 'Post',
                    'class' => 'article',
                    'url' => ['/administrator/post/'],
                    'activeIf' => [
                        'mainUrl' => 'content',
                        'subUrl' => ['post']
                    ]
                ],
                [
                    'label' => 'Categories',
                    'class' => 'category',
                    'url' => ['/administrator/category/'],
                    'activeIf' => [
                        'mainUrl' => 'content',
                        'subUrl' => ['category']
                    ]
                ],
                [
                    'label' => 'Article Comments',
                    'class' => 'comment',
                    'url' => ['/administrator/comment/'],
                    'activeIf' => [
                        'mainUrl' => 'content',
                        'subUrl' => ['comment']
                    ]
                ]
            ];
            $placesMenuList = '';
            foreach ($menus as $menu) {
                $placesMenuList .= renderMenu($menu);
            }

            echo Html::tag('ul', $placesMenuList, ['class'=>'submenu']);
            ?>
        </li>
        <?php
        // prepare submenu configuration
        $menuConfigurations = [];
        $controllers = ['configuration'];
        if(isset($cms['configuration'])){
            $menus = $cms['configuration'];
            unset($cms['configuration']);
            $menuConfigurations = $menus['childs'];

        }

        $placesMenuList = '';
        $menus = [
            [
                'label' => 'General',
                'class' => 'general',
                'url' => ['/administrator/configuration/general'],
                'activeIf' => [
                    'mainUrl' => 'configuration',
                    'subUrl' => ['general']
                ],
            ],
            [
                'label' => 'Frontend Menu',
                'class' => 'frontend-menu',
                'url' => ['/administrator/configuration/frontend-menu'],
                'activeIf' => [
                    'mainUrl' => 'configuration',
                    'subUrl' => ['frontend-menu']
                ]
            ]
        ];

        $menus = array_merge($menus, $menuConfigurations);
        $placesMenuList = '';
        foreach ($menus as $menu) {
            if(!in_array($menu['activeIf']['mainUrl'], $controllers)){
                $controllers[] = $menu['activeIf']['mainUrl'];
            }

            $placesMenuList .= renderMenu($menu);
        }

        ?>

        <li class="<?= (in_array($GLOBALS['mainUrl'], $controllers)) ? 'open' : ''?>">
            <?= Html::a(Icon::glyph('cog'). Html::tag('span', '&nbsp; Web Options', ['class'=>'menu-text']), 'javascript:', ['class'=>'menu-dropdown']) ?>
            <?= Html::tag('ul', $placesMenuList, ['class'=>'submenu']); ?>
        </li>
        <li class="<?= ($GLOBALS['mainUrl'] =='user') ? 'open' : ''?>">
            <?= Html::a(Icon::glyph('user'). Html::tag('span', '&nbsp; User Management', ['class'=>'menu-text']), 'javascript:', ['class'=>'menu-dropdown']) ?>
            <?php
            $placesMenuList = '';
            $placesMenuList .= Html::tag('li', Html::a(Html::tag('span', 'User Type', ['class'=>'menu-text page']), ['/administrator/user/user-types']), ['class'=>$GLOBALS['subUrl']=='user-types'? 'active' : '']);
            $placesMenuList .= Html::tag('li', Html::a(Html::tag('span', 'Users', ['class'=>'menu-text page']), ['/administrator/user/']), ['class'=>$GLOBALS['subUrl']=='' || $GLOBALS['subUrl']=='index'? 'active' : '']);
            $placesMenuList .= Html::tag('li', Html::a(Html::tag('span', 'Access Control', ['class'=>'menu-text page']), ['/administrator/user/access-control']), ['class'=>$GLOBALS['subUrl']=='access-control'? 'active' : '']);
            echo Html::tag('ul', $placesMenuList, ['class'=>'submenu']);
            ?>
        </li>

        <?= $this->render('client-menu', [
            'cms' => $cms,
            'mainUrl' => $GLOBALS['mainUrl'],
            'subUrl' => $GLOBALS['subUrl']
        ]); ?>
    </ul>
</div>
