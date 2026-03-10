<?php
if (!function_exists('display_menu_module')) {
    function display_menu_module($category_id, $parent, $level)
    {
        $baseModel = new \App\Models\BaseModel();
        $query = $baseModel->query(
            "select a.id, a.name as label, a.type, a.active, a.controller, a.slug, deriv.count 
            from c_menus a left outer join (
                select parent, count(*) as count 
                    from c_menus group by parent
                ) deriv on a.id = deriv.parent where  a.parent= " .
                $parent .
                ' and a.category_id = ' .
                $category_id .
                "
            order by `sort` asc"
        );
        $result = $query->getResult();

        $ret = '';

        if ($result) {
            $ret .= '<ol class="dd-list">';
            foreach ($result as $row) {
                if ($row->count > 0) {
                    $ret .=
                        '<li class="dd-item dd3-item ' .
                        ($row->active ? '' : 'menu-toggle-activate_inactive') .
                        ' menu-toggle-activate" data-id="' .
                        $row->id .
                        '" data-status="' .
                        $row->active .
                        '">';

                    if ($row->type != 'label') {
                        $ret .=
                            '<div class="dd-handle dd3-handle dd-handles"></div>';
                        $ret .= '<div class="dd3-content">' . _ent($row->label);
                    } else {
                        $ret .=
                            '<div class="dd-handle dd3-handle dd-handles dd-handle-label"></div>';
                        $ret .=
                            '<div class="dd3-content "><b>' .
                            _ent($row->label) .
                            '</b>';
                    }

                    $ret .= '</div>';
                    $ret .= display_menu_module(
                        $category_id,
                        $row->id,
                        $level + 1
                    );
                    $ret .= '</li>';
                } elseif ($row->count == 0) {
                    $ret .=
                        '<li class="dd-item dd3-item ' .
                        ($row->active ? '' : 'menu-toggle-activate_inactive') .
                        ' menu-toggle-activate" data-id="' .
                        $row->id .
                        '" data-status="' .
                        $row->active .
                        '">';

                    if ($row->type != 'label') {
                        $ret .=
                            '<div class="dd-handle dd3-handle dd-handles"></div>';
                        $ret .=
                            '<div class="dd3-content"><span data-toggle="tooltip" data-placement="top" title="c:' .
                            $row->controller .
                            '">' .
                            _ent($row->label) .
                            '</span>';
                    } else {
                        $ret .=
                            '<div class="dd-handle dd3-handle dd-handles dd-handle-label"></div>';
                        $ret .=
                            '<div class="dd3-content  "><b>' .
                            _ent($row->label) .
                            '</b>';
                    }

                    $ret .= display_menu_dropdown($row->id, $row->controller);

                    $ret .= '</div></li>';
                }
            }
            $ret .= '</ol>';
        }

        return $ret;
    }
}

if (!function_exists('display_menu_dropdown')) {
    function display_menu_dropdown($id, $controller)
    {
        $action = '';
        $action .= '<div class="pull-right">';
        $action .=
            '<div class="btn-group" style="margin-top:-5px">
						<a href="' .
            base_url('reference?menu_id=' . $id) .
            '" data-toggle="tooltip" data-placement="top" title="Daftar Item" class="btn btn-sm btn-primary"><i class="lnr-list font-weight-bold"></i></a>
						<button type="button" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown" class="dropdown-toggle-split dropdown-toggle btn btn-primary btn-sm"><span class="sr-only">Dropdown</span> </button>
						<div tabindex="-1" role="menu" aria-hidden="true" class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(51px, 29px, 0px);">
							<button type="button" tabindex="0" class="dropdown-item" data-toggle="modal" data-target="#modal_detail_category"><i class="dropdown-icon lnr-pencil"> </i><span>Ubah</span></button>
							<button type="button" tabindex="1" class="dropdown-item" data-toggle="modal" data-target="#modal_create_category"><i class="dropdown-icon lnr-plus-circle"> </i><span>Sub Menu</span></button>
							<button type="button" tabindex="2" class="dropdown-item"><i class="dropdown-icon lnr-trash remove-data"> </i><span>Hapus</span></button>
						</div>
					</div>';
        $action .= '</div>';
        return $action;
    }
}

if (!function_exists('display_menu_backend')) {
    function display_menu_backend($parent, $level = 1, $where = false)
    {
        $request = \Config\Services::request();
        $request->uri->setSilent();
        $baseModel = new \App\Models\BaseModel();

        $where = $where ?? '';

        $query = $baseModel->query(
            "select a.id, a.icon, a.name, a.controller, a.type, deriv.childs 
            from c_menus a left outer join (
                select parent, count(*) as childs 
                    from c_menus group by parent
                ) deriv on a.id = deriv.parent WHERE a.parent= " .
                $parent .
                " and a.active = 1 and a.category_id = '1' " .
                $where .
                " 
            order by `sort` asc"
        );

        $result = $query->getResult();

        $ret = '';
        if ($result) {
            if ($level > 1 and $parent > 0) {
                $ret .= '<ul>';
            } else {
                $ret = '';
            }
            foreach ($result as $row) {
                if (!is_accessed($row->controller)) {
                    continue;
                }
                $active =
                    strtolower($request->uri) ==
                    strtolower(base_url($row->controller))
                        ? 'mm-active'
                        : '';
                $link = base_url($row->controller);
                $style =
                    substr($row->icon, 0, 2) == 'fa' ? 'font-size:20px' : '';

                if ($row->type == 'label') {
                    $ret .=
                        '<li class="app-sidebar__heading">' .
                        $row->name .
                        '</li>';
                } else {
                    if ($row->childs > 0) {
                        $ret .= '<li class="' . $active . ' ">';
                        $ret .=
                            '<a href="#" class="' .
                            $active .
                            '"><i class="metismenu-icon ' .
                            $row->icon .
                            '" style="' .
                            $style .
                            '"></i>' .
                            $row->name .
                            ' <i class="metismenu-state-icon pe-7s-angle-down caret-left"></i></a>';
                        $ret .= display_menu_backend(
                            $row->id,
                            $level + 1,
                            $where
                        );
                        $ret .= '</li>';
                    } else {
                        $ret .= '<li class="' . $active . ' ">';
                        $ret .=
                            '<a href="' .
                            $link .
                            '" class="' .
                            $active .
                            '"><i class="metismenu-icon ' .
                            $row->icon .
                            '" style="' .
                            $style .
                            '"></i>' .
                            $row->name .
                            '</a>';
                        $ret .= '</li>';
                    }
                }
            }
            if ($level > 1) {
                $ret .= '</ul>';
            }
        }
        return $ret;
    }
}

if (!function_exists('display_menu_frontend')) {
    function display_menu_frontend($parent, $level = 1)
    {
        $request = \Config\Services::request();
        $request->uri->setSilent();
        $baseModel = new \App\Models\BaseModel();

        $query = $baseModel->query(
            "SELECT a.id, a.icon, a.name, a.controller, a.type, a.category_id, deriv.childs 
			FROM `c_menus` a LEFT OUTER JOIN (
				SELECT parent, COUNT(*) AS childs 
					FROM `c_menus` GROUP BY parent
				) deriv ON a.id = deriv.parent WHERE  a.parent= " .
                $parent .
                " and a.active = 1 and a.category_id = '2' 
			ORDER BY `sort` ASC"
        );
        $result = $query->getResult();

        $ret = '';
        if ($result) {
            if ($level > 1 and $parent > 0) {
                $ret .= '<ul class="dropdown">';
            } else {
                $ret = '';
            }

            $class_li = 'menu-item ';
            $class_a = ' ';

            foreach ($result as $row) {
                $link = base_url($row->controller);
                $active =
                    strtolower($request->uri) == strtolower($link)
                        ? 'active'
                        : '';

                if ($level > 1) {
                    $class_li = '';
                    $class_a = '';
                }

                if ($row->childs > 0) {
                    $ret .= '<li class="' . $class_li . ' child">';
                    $ret .=
                        '<a href="#" class="' .
                        $class_a .
                        '">' .
                        $row->name .
                        '</a>';
                    $ret .= display_menu_frontend($row->id, $level + 1);
                    $ret .= '</li>';
                } else {
                    $ret .= '<li class="' . $class_li . '' . $active . '">';
                    $ret .=
                        '<a href="' .
                        $link .
                        '" class="' .
                        $class_a .
                        '">' .
                        $row->name .
                        '</a>';
                    $ret .= '</li>';
                }
            }
            if ($level > 1) {
                $ret .= '</ul>';
            }
        }
        return $ret;
    }
}

if (!function_exists('config_menu_frontend')) {
    function config_menu_frontend($parent, $level = 1)
    {
        $db = db_connect();
        $builder = $db
            ->table('c_menus as a')
            ->select(
                '0 as themes, a.id as key, "true" as is_public, a.name as label, a.description as label_en, a.controller as url'
            )
            ->select('deriv.childs as submenu')
            ->join(
                '(select parent, COUNT(*) AS childs from c_menus group by parent) deriv',
                'deriv.parent = a.id',
                'left'
            )
            ->where('a.parent', $parent)
            ->where('a.active', 1)
            ->where('a.category_id', 2)
            ->orderBy('a.sort', 'asc');

        $result = $builder->get()->getResult();
        foreach ($result as $idx => $row) {
            $result[$idx]->themes = (int) $row->themes;
            $result[$idx]->is_public = (bool) $row->is_public;
            $result[$idx]->label_en = empty($row->label_en)
                ? $row->label
                : $row->label_en;
            // $result[$idx]->url = permalink($result[$idx]->url);
            if ($row->submenu) {
                $result[$idx]->submenu = config_menu_frontend($row->key);
            } else {
                $result[$idx]->submenu = [];
            }
        }

        return $result;
    }
}
