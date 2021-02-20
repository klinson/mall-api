<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Actions\CopyInfoButton;
use App\Admin\Extensions\Tools\DefaultSimpleTool;
use App\Models\Category;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Show;
use Encore\Admin\Tree;
use Encore\Admin\Widgets\Box;

class CategoriesController extends Controller
{
    use ModelForm;

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '分类管理';

    public function index()
    {
        return Admin::content(function (Content $content) {
            $this->_setPageDefault($content);
            $content->row(function (Row $row) {
                $row->column(6, $this->treeView()->render());

                $row->column(6, function (Column $column) {
                    $form = new \Encore\Admin\Widgets\Form();
                    $form->action(admin_base_path('categories'));

                    $form->select('parent_id', '上级分类')->options(Category::selectOptions());
                    $form->text('title', __('Title'))->required();
                    $form->image('thumbnail', __('Thumbnail'))->uniqueName();
                    $form->switch('is_recommended', '是否推荐')->default(0)->rules('required');
                    $form->switch('has_enabled', __('Has enabled'))->default(1);
                    $form->number('sort', __('Sort'))->default(0);

//                    $form->textarea('description', '描述');
//                    $form->hidden('_token')->default(csrf_token());

                    $column->append((new Box(trans('admin.new'), $form))->style('success'));
                });
            });

        });
    }

    protected function treeView()
    {
        Admin::script(
            <<<HTML
(new Clipboard('.clipboard-url-btn')).on('success', function(e) {
    alert('复制代码['+e.text+']成功');

    e.clearSelection();
});
HTML
        );
        return Category::tree(function (Tree $tree) {
            $tree->tools(function (Tree\Tools $tools) {
                $tools->add(new DefaultSimpleTool(admin_base_path('categories/resetCache'), '刷新缓存', 'right', 'warning', 'refresh'));
            });
            $tree->disableCreate();

            $tree->branch(function ($branch) {
                $payload = '';
                if ($branch['is_recommended']) {
                    $payload .= "<i class='fa fa-arrow-circle-o-up text-red'></i>&nbsp;";
                }
                $payload .= "<strong>{$branch['title']}</strong>";
                $payload .= "<span class='pull-right dd-nodrag'><a class='clipboard-url-btn' data-clipboard-text='category-{$branch['id']}' href='javascript:void(0);' style='margin-left: 3px'><i class='fa fa-clipboard'></i></a></span>";

                return $payload;
            });
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Category);

        $grid->column('id', __('Id'));
        $grid->column('title', __('Title'));
        $grid->column('thumbnail', __('Thumbnail'))->image();
        grid_has_enabled($grid);
        $grid->column('sort', __('Sort'));
        $grid->column('created_at', __('Created at'));

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            $actions->append(new CopyInfoButton(
                '复制代码',
                $this->row->ad_code
            ));
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
        $show = new Show(Category::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Title'));
        $show->field('full_title', __('Full title'));
        $show->field('thumbnail', __('Thumbnail'))->image();
        $show->field('is_recommended', '是否推荐')->using(YN2TEXT);
        $show->field('has_enabled', __('Has enabled'))->using(HAS_ENABLED2TEXT);
        $show->field('sort', __('Sort'));
        $show->field('created_at', __('Created at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Category);

        $form->text('title', __('Title'))->required();
        $form->hidden('full_title', __('Full_title'));
        $form->image('thumbnail', __('Thumbnail'))->uniqueName();
        $form->select('parent_id', '上级分类')->options(Category::selectOptions());
        $form->switch('is_recommended', '是否推荐')->default(0)->rules('required');
        $form->switch('has_enabled', __('Has enabled'))->default(1);
        $form->number('sort', __('Sort'))->default(0);

        $form->saving(function ($form) {
        });

        return $form;
    }

    public function resetCache()
    {
        Category::getByCache(true);
        admin_success('刷新缓存成功');
        return redirect(admin_base_path('categories'));
    }

    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {
            $this->_setPageDefault($content);

            $content->body($this->form()->edit($id));
        });
    }

    public function import()
    {
        $content = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'1.csv');
        $arr = explode("\r\n", $content);
        $map = [
            '图书' => 1,
            '文具' => 2,
        ];
        foreach ($arr as $item) {
            $item = explode(',', $item);
            $data = [
                'title' => substr($item[1], 3),
                'code' => substr($item[1], 0, 3),
                'parent_id' => $map[$item[0]],
            ];
            Category::create($data);
        }
        dd($arr);
    }
}
