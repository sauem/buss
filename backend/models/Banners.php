<?php

namespace backend\models;

use common\helper\Helper;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * This is the model class for table "banners".
 *
 * @property int $id
 * @property string|null $title
 * @property float|null $height
 * @property float|null $width
 * @property string|null $position
 * @property int|null $sort
 * @property string|null $href
 * @property string|null $active
 * @property string|null $page
 * @property int|null $type
 * @property int|null $is_random
 * @property int|null $bellow_post
 * @property string|null $device
 * @property string|null $domain
 * @property string|null $youtube_url
 * @property string|null $time_range
 * @property int $created_at
 * @property int $updated_at
 * @property int|null $timer_start
 * @property int|null $timer_end
 *
 * @property StatisticReport[] $statisticReports
 */
class Banners extends \common\models\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public $thumb;
    public $avatar;
    public $public;
    public $time_range;

    const STATUS_ACTIVE = 'active';
    const STATUS_DEACTIVE = 'deactive';
    const STATUS = [
        self::STATUS_ACTIVE => 'Kích hoạt',
        self::STATUS_DEACTIVE => 'Ngưng kích hoạt'
    ];

    const TYPE_IMAGE = 1;
    const TYPE_VIDEO = 2;
    const TYPE = [
        self::TYPE_IMAGE => 'Hình ảnh',
        self::TYPE_VIDEO => 'Video'
    ];

    const POSITION_TOP = 'top';
    const POSITION_RIGHT = 'right';
    const POSITION_LEFT = 'left';
    const POSITION_BOTTOM = 'bottom';
    const POSITION_CONTENT = 'content';

    const POSITION = [
        self::POSITION_TOP => 'Phía trên',
        self::POSITION_LEFT => 'Cột trái',
        self::POSITION_RIGHT => 'Cột phải',
        self::POSITION_BOTTOM => 'Phía dưới',
        self::POSITION_CONTENT => 'Giữa bài viết'
    ];

    const PAGE_HOME = 'home';
    const PAGE_POST = 'post';
    const PAGE_ARCHIVE = 'archive';

    const PAGE = [
        self::PAGE_HOME => 'Trang chủ',
        self::PAGE_ARCHIVE => 'Danh mục',
        self::PAGE_POST => 'Bài viết'
    ];
    const STYLE_RANDOM = 0;
    const STYLE_STATIC = 1;

    const STYLE = [
        self::STYLE_RANDOM => 'Ngẫu nhiên',
        self::STYLE_STATIC => 'Cố định'
    ];

    const DEVICE_MOBILE = 'mobile';
    const DEVICE_DESKTOP = 'desktop';

    const DEVICE = [
        self::DEVICE_DESKTOP => 'Desktop',
        self::DEVICE_MOBILE => 'Mobile'
    ];

    public static function tableName()
    {
        return 'banners';
    }

    /**
     * {@inheritdoc}
     */
    public function getReport()
    {
        return $this->hasMany(StatisticReport::className(), ['banner_id' => 'id']);
    }

    public function rules()
    {
        return [
            [['height', 'width'], 'number'],
            [['sort', 'type', 'is_random', 'bellow_post', 'created_at', 'updated_at', 'timer_start', 'timer_end'], 'integer'],
            [['title', 'page', 'position', 'active', 'device', 'type', 'is_random'], 'required'],
            [['title', 'href', 'domain'], 'string', 'max' => 255],
            [['position'], 'string', 'max' => 100],
            [['active', 'device'], 'string', 'max' => 50],
            [['thumb', 'avatar', 'page', 'youtube_url', 'time_range'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeSave($insert)
    {
        if ($this->type === self::TYPE_IMAGE && !$this->thumb) {
            $this->addError('thumb', 'Hình ảnh không được để trống!');
            return false;
        }
        if ($this->page && is_array($this->page)) {
            $this->page = implode(',', $this->page);
        }
        if ($this->time_range && !empty($this->time_range)) {
            $times = explode(' - ', $this->time_range);
            $startAt = Helper::timer(str_replace('/', '-', $times[0]));
            $endAt = Helper::timer(str_replace('/', '-', $times[1]));
            $this->timer_start = $startAt ? $startAt : null;
            $this->timer_end = $endAt ? $endAt : null;
        }
        if ($insert) {
            if (self::existPosition($this->position, $this->page, $this->device)
                && $this->is_random == self::STYLE_STATIC) {
                $this->addError('position', 'Đã tồn tại banner tĩnh với vị trí tương ứng!');
                return false;
            }
        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public static function existPosition($position, $page, $device)
    {
        $banner = Banners::find()->where([
            'position' => $position,
            'device' => $device,
            'type' => self::STYLE_STATIC])
            ->andWhere(['LIKE', 'page', $page])->one();
        if (!$banner) {
            return false;
        }
        return true;
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Tiêu đề',
            'height' => 'Chiều cao',
            'width' => 'Chiều rộng',
            'position' => 'Vị trí',
            'sort' => 'Thứ tự',
            'href' => 'Liên kết',
            'active' => 'Trạng thái',
            'page' => 'Trang hiển thị',
            'type' => 'Phân loại',
            'is_random' => 'Ngẫu nhiên',
            'bellow_post' => 'Hiển thị tại dòng',
            'device' => 'Thiết bị',
            'domain' => 'Domain',
            'thumb' => 'Hình ảnh',
            'time_start' => 'Ngày bắt đầu',
            'time_end' => 'Giờ kết thúc',
            'youtube_url' => 'Link video',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getMedia()
    {
        return $this->hasOne(MediaObj::className(),
            ['obj_id' => 'id'])
            ->where(['{{media_obj}}.obj_type' => MediaObj::OBJECT_BANNER])
            ->with('media');
    }


    public function afterFind()
    {
        if ($this->page && !empty($this->page)) {
            $this->page = explode(',', $this->page);
        }
        if (!empty($this->timer_start) && !empty($this->timer_end)) {
            $this->time_range = date('d/m/Y', $this->timer_start) . ' - ' . date('d/m/Y', $this->timer_end);
        }
        $this->avatar = $this->media ? $this->media->media->url : Helper::defaultImage('product');
        $this->thumb = $this->media ? $this->media->media->id : null;
        parent::afterFind(); // TODO: Change the autogenerated stub
    }

    static function bannerLabel($status)
    {
        switch ($status) {
            case self::STATUS_ACTIVE:
                $color = 'success';
                break;
            default:
                $color = 'danger';
        }
        return Html::tag('span', ArrayHelper::getValue(self::STATUS, $status), [
            'class' => "badge badge-pill m-auto badge-$color"
        ]);
    }

    static function pageLabel($pages)
    {
        $html = "";
        if (!empty($pages) && is_array($pages)) {
            foreach ($pages as $page) {
                $html .= Html::tag('span', ArrayHelper::getValue(self::PAGE, $page), [
                    'class' => "badge badge-pill m-auto badge-secondary"
                ]);
            }
        } else {
            $html = '---';
        }
        return $html;
    }

    public function getStatisticReports()
    {
        return $this->hasMany(StatisticReport::className(), ['banner_id' => 'id']);
    }
}
