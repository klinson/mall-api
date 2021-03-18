<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Actions\GetButton;
use App\Admin\Extensions\Tools\DefaultSimpleTool;
use App\Models\Author;
use App\Models\Category;
use App\Models\Goods;
use App\Models\GoodsSpecification;
use App\Models\Press;
use Doctrine\DBAL\Driver\OCI8\Driver;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Show;
use App\Admin\Extensions\Actions\CopyInfoButton;
use Encore\Admin\Widgets\Box;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class GoodsController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '商品管理';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Goods);

        $grid->model()->with(['specifications'])->recent();

        $grid->column('id', __('Id'));
        grid_display_relation($grid, 'category');
        $grid->column('title', __('Title'));
        $grid->column('isbn', __('Isbn'));
        $grid->column('thumbnail', __('Thumbnail'))->image();
        $grid->column('max_price', __('Max price'))->currency();
        $grid->column('min_price', __('Min price'))->currency();

        $states = [
            'on'  => ['value' => 1, 'text' => '打开', 'color' => 'primary'],
            'off' => ['value' => 0, 'text' => '关闭', 'color' => 'default'],
        ];
        $grid->column('has_enabled', __('Has enabled'))->switch($states)->filter(HAS_ENABLED2TEXT);
        $grid->column('has_recommended', __('Has recommended'))->filter(YN2TEXT)->switch($states);
        $grid->column('sort', __('Sort'));
//        $grid->column('created_at', __('Created at'))->sortable()->filter('range', 'datetime');
        $grid->column('updated_at', __('Updated at'))->sortable()->filter('range', 'datetime');

        $grid->column('specifications', '商品规格')->display(function () {
            if (empty($this->specifications)) {
                $specificationList = [];
            } else if (is_array($this->specifications)) {
                $specificationList = $this->specifications;
            } else {
                $specificationList = $this->specifications->toArray();
            }
            $specifications = array_map(function ($item) {
                return [
                    '规格名称' => $item['title'],
                    '售价' => '￥'.strval($item['price'] * 0.01),
                    '库存' => $item['quantity'],
                    '销量' => $item['sold_quantity'],
                    '重量' => $item['weight'],
                    '启用？' => HAS_ENABLED2TEXT[$item['has_enabled']],
                ];
            }, $specificationList);
            return $specifications;
        })->table();

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->append(new CopyInfoButton(
                '复制代码',
                $this->row->ad_code
            ));
        });

        $grid->tools(function (Grid\Tools $tools) {
            $tools->append(new DefaultSimpleTool(admin_base_path('goods/import2'), '批量导入2', 'right', 'warning', 'upload'));
            $tools->append(new DefaultSimpleTool(admin_base_path('goods/import'), '批量导入', 'right', 'warning', 'upload'));
        });

        $grid->filter(function ($filter) {
            $filter->like('title', '名称');
            $filter->like('isbn', 'Isbn');
            $filter->equal('category_id', '所属分类')->select(Category::selectOptions(null, '所有'));

        });
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Goods::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
        $show->field('isbn', __('Isbn'));
        show_display_relation($show, 'category');
        $show->field('thumbnail', __('Thumbnail'))->image();
        $show->field('publish_date', __('Publish date'));
        show_display_relation($show, 'press');
//        show_display_relation($show, 'authors');
        $show->field('authors', __('Authors'))->as(function ($list) {
            return $list->pluck('name')->toArray();
        })->label();
        $show->field('description', __('Description'));
        $show->field('images', __('Images'))->image();
        $show->field('detail', __('Detail'))->unescape();
        $show->field('max_price', __('Max price'))->as(function ($item) {
            return '￥'.$item*0.01;
        });
        $show->field('min_price', __('Min price'))->as(function ($item) {
            return '￥'.$item*0.01;
        });
        $show->field('has_enabled', __('Has enabled'))->using(HAS_ENABLED2TEXT);
        $show->field('has_recommended', __('Has recommended'))->using(YN2TEXT);
        $show->field('sort', __('Sort'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        $show->specifications('规格', function (Grid $grid) {
            $grid->column('id', __('Id'));
            $grid->column('title', __('Title'));
            $grid->column('barcode', __('Barcode'));
            $grid->column('thumbnail', __('Thumbnail'))->image();
            $grid->column('price', __('Price'))->currency();
            $grid->column('quantity', __('Quantity'));
            $grid->column('sold_quantity', __('Sold quantity'));
            $grid->column('weight', __('Weight'));
            $grid->column('has_enabled', __('Has enabled'))->using(HAS_ENABLED2TEXT);
            $grid->column('sort', __('Sort'));
            $grid->column('created_at', __('Created at'));
            $grid->column('updated_at', __('Updated at'));

            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableFilter();
            $grid->disableRowSelector();
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                 $actions->disableView();
                 $actions->disableEdit();
                 $actions->disableDelete();
                 $actions->append(new GetButton('/admin/discountGoods/create?goods_specification_id='.$actions->getKey(), '设置折扣'));
            });
        });


        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Goods);

        $form->select('category_id', __('Category id'))->options(Category::selectOptions(null, null))->required();
        $form->text('title', __('Title'))->required();
        $form->text('isbn', __('Isbn'))->required();
        $form->image('thumbnail', __('Thumbnail'))->uniqueName();
        $form->multipleImage('images', __('Images'))->uniqueName()->removable();
        $form->textarea('description', __('Description'));
        $form->editor('detail', __('Detail'));

        $form->date('publish_date', __('Publish date'))->format('YYYY-MM');
        Press::form_display_select($form, 'press_id')->default(1);
        Author::form_display_select($form, 'authors', 'name', __('Authors'), false, 'name', 'multipleSelect');
//        $form->multipleSelect('authors', __('Authors'))->options(Author::all()->pluck('name', 'id'));

        $form->switch('has_enabled', __('Has enabled'))->default(1);
        $form->switch('has_recommended', __('Has recommended'))->default(1);
        $form->number('sort', __('Sort'))->default(0);

        $form->hasMany('specifications', '商品规格',  function (Form\NestedForm $form) {
            $form->text('title', __('Title'))->rules('required');
            $form->text('barcode', __('Barcode'))->required();
            $form->image('thumbnail', __('Thumbnail'))->uniqueName()->removable()->addElementClass('specifications_'.random_string(10));
            $form->currency('price', __('Price'))->rules(['required', 'min:1', 'numeric']);
            $form->number('quantity', __('Quantity'))->default(1)->rules(['required', 'integer', 'min:0']);
            $form->number('sold_quantity', __('Sold quantity'))->default(0)->rules(['required', 'integer', 'min:0']);
            $form->weight('weight', __('Weight'))->default(0)->rules(['required', 'min:0']);
            $form->number('sort', __('Sort'))->default(0);
            $form->switch('has_enabled', __('Has enabled'))->default(1);
        });

        $form->saving(function ($form) {
            if (is_null($form->images)) {
                if ($form->thumbnail) {
                    $form->images = [$form->thumbnail];
                } else {
                    $form->images = [];
                }
            };
//            $form->authors = array_filter($form->authors);

//            $form->authors = [[1], [2]];
        });

        return $form;
    }

    public function import2(Request $request, Content $content)
    {
        if ($request->isMethod('post')) {
            $file = $request->file('file');
            if (empty($file)) {
                admin_error('请上传导入文件');
                return redirect()->back();
            }
            if (empty($request->category_id)) {
                admin_error('请选择分类');
                return redirect()->back();
            }
            if (blank($request->quantity) || $request->quantity < 0) {
                admin_error('请输入库存');
                return redirect()->back();
            }
            $request->quantity = intval($request->quantity);

            $reader = \PHPExcel_IOFactory::createReaderForFile($file->getRealPath());
            $objPHPExcel = $reader->load($file->getRealPath());
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = 'D';
            $first = 3;
            $list = $sheet->rangeToArray('A' . $first . ':' . $highestColumn . $highestRow, NULL, TRUE, FALSE);
            //处理图片
            $imageFilePath = Storage::disk('admin')->path('images') . DIRECTORY_SEPARATOR .date('Ymd').DIRECTORY_SEPARATOR;//图片在本地存储的路径
            if (! file_exists ( $imageFilePath )) {
                @mkdir("$imageFilePath", 0777, true);
            }
            foreach($sheet->getDrawingCollection() as $img) {
                list($startColumn,$startRow)= \PHPExcel_Cell::coordinateFromString($img->getCoordinates());//获取图片所在行和列

                $imgFile = $img->getHashCode().'.'.$img->getExtension();
                copy($img->getPath(),$imageFilePath.$imgFile);

                $startColumn = ABC2decimal($startColumn);//由于图片所在位置的列号为字母，转化为数字
                // bug
                $list[$startRow-$first][$startColumn][] = 'images'.DIRECTORY_SEPARATOR.date('Ymd').DIRECTORY_SEPARATOR.$imgFile;//把图片插入到数组中
            }

//            dd($list);

            $update_count = $create_count = 0;
            foreach ($list as $data) {
//                $data = array_filter(array_map('trim', $data));

                $goods_data = [
                    'title' => $data[1],
                    'category_id' => $request->category_id,
                    'images' => (isset($data[4]) && !empty($data[4])) ? $data[4] : [],
                ];
                if ($goods_data['images']) {
                    $goods_data['thumbnail'] = $goods_data['images'][0];
                }
                if (empty($goods_data['title'])) continue;

                $where = ['title' => $goods_data['title']];
                if ($goods = Goods::where($where)->first()) {
                    if (empty($goods_data['isbn'])) unset($goods_data['isbn']);
                    if (empty($goods_data['images'])) unset($goods_data['images']);
                    $goods->fill($goods_data);
                    $goods->save();
                    $update_count++;
                } else {
                    $goods = Goods::create($goods_data);
                    $create_count++;
                }

                $tmp = array_values(array_filter(array_map('trim', explode("\n", $data[2]))));
                foreach ($tmp as $ss) {
                    $tmpp = explode('：', $ss);

                    $specification = [
                        'goods_id' => $goods->id,
                        'title' => count($tmpp) >= 2 ? $tmpp[0] : $goods_data['title'],
                        'price' => floatval(trim($data[3], '¥ ')) * 100,
                        'quantity' => $request->quantity,
                        'barcode' => count($tmpp) >= 2 ? $tmpp[1] : $tmpp[0],
                    ];
                    $s = GoodsSpecification::where('barcode', $specification['barcode'])->first();
                    if ($s) {
                        $s->fill($specification);
                        $s->save();
                    } else {
                        $s = GoodsSpecification::create($specification);
                    }
                }
            }
            admin_success('导入成功', "新增{$create_count}条记录，更新{$update_count}条记录");
            return redirect()->refresh();
        } else {
            $content->title('批量导入商品');
            $form = new \Encore\Admin\Widgets\Form();
            $form->action(admin_base_path('/goods/import2'));
            $form->method();
            $form->select('category_id', __('Category id'))->options(Category::selectOptions(null, null))->required();
            $form->number('quantity', __('Quantity'))->default(10)->required();
            $form->file('file', '导入文件')->uniqueName()->required()->help('注意：导入会覆盖同名字商品');
            $form->html("<p>模板下载：<a target='_blank' href='".url('/downloads/templates/import-goods2.xlsx')."'>导入模板</a></p>");
            $content->row(function (Row $row) use ($form) {
                $row->column(12, new Box('', $form));
            });
            return $content;
        }

    }

    public function import(Request $request, Content $content)
    {
        if ($request->isMethod('post')) {
            $file = $request->file('file');
            if (empty($file)) {
                admin_error('请上传导入文件');
                return redirect()->back();
            }
            if (empty($request->category_id)) {
                admin_error('请选择分类');
                return redirect()->back();
            }

            $reader = \PHPExcel_IOFactory::createReaderForFile($file->getRealPath());
            $objPHPExcel = $reader->load($file->getRealPath());
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
            $first = 5;
            $list = $sheet->rangeToArray('A' . $first . ':' . $highestColumn . $highestRow, NULL, TRUE, FALSE);
            $update_count = $create_count = 0;
            foreach ($list as $data) {
                $data = array_map('trim', $data);

                $press = Press::firstOrCreate(['title' => $data[2]]);
                $goods_data = [
                    'title' => $data[1],
                    'category_id' => $request->category_id,
                    'press_id' => $press->id,
                    'isbn' => ($data[8] && $data[8] != '不区分' ? $data[8] : ''),
                    'images' => [],
                ];
                if (empty($goods_data['title'])) continue;

                $where = ['title' => $goods_data['title']];
                if ($goods = Goods::where($where)->first()) {
                    if (empty($goods_data['isbn'])) unset($goods_data['isbn']);
                    if (empty($goods_data['images'])) unset($goods_data['images']);
                    $goods->fill($goods_data);
                    $goods->save();
                    $update_count++;
                } else {
                    $goods = Goods::create($goods_data);
                    $create_count++;
                }

                $specification = [
                    'goods_id' => $goods->id,
                    'title' => $data[1],
                    'price' => $data[3] * 100,
                    'quantity' => $data[5],
                    'barcode' => $data[0],
                ];
                if ($goods->specifications->count() == 1) {
                    $s = $goods->specifications[0];
                    if (blank($specification['barcode'])) unset($specification['barcode']);
                    if (blank($specification['price'])) unset($specification['price']);
                    if (blank($specification['quantity'])) unset($specification['quantity']);

                    $s->fill($specification);
                    $s->save();
                } else {
                    $s = GoodsSpecification::create($specification);
                }
            }
            admin_success('导入成功', "新增{$create_count}条记录，更新{$update_count}条记录");
            return redirect()->refresh();

        } else {
            $content->title('批量导入商品');
            $form = new \Encore\Admin\Widgets\Form();
            $form->action(admin_base_path('/goods/import'));
            $form->method();
            $form->select('category_id', __('Category id'))->options(Category::selectOptions(null, null))->required();
            $form->file('file', '导入文件')->uniqueName()->required()->help('注意：导入会覆盖同名字商品');
            $form->html("<p>模板下载：<a target='_blank' href='".url('/downloads/templates/import-goods.xlsx')."'>导入模板</a></p>");
            $content->row(function (Row $row) use ($form) {
                $row->column(12, new Box('', $form));
            });
            return $content;
        }

    }
}
