<?php
namespace Weixin\Model;

use Think\Model;

class AddonConfModel extends Model {
	protected $fields = array(
		'id',
		'addon_id',
		'configure',
	);

}
