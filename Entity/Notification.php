<?php
/**
 * Created by PhpStorm.
 * User: shate
 * Date: 2014/09/15
 * Time: 12:05
 */
namespace GaroonTools\Entity;

class Notification {
	const MODULE_ID_GRN_SCHEDULE = 'grn.schedule'; // スケジュール
	const MODULE_ID_GRN_MESSAGE = 'grn.message'; // メッセージ
	const MODULE_ID_GRN_BULLETIN = 'grn.bulletin'; // 掲示板
	const MODULE_ID_GRN_CABINET = 'grn.cabinet'; // ファイル管理
	const MODULE_ID_GRN_PHONEMESSAGE = 'grn.phonemessage'; // 電話メモ
	const MODULE_ID_GRN_MAIL = 'grn.mail'; // メール
	const MODULE_ID_GRN_WORKFLOW = 'grn.workflow'; // ワークフロー
	const MODULE_ID_GRN_REPORT = 'grn.report'; // マルチレポート
	const MODULE_ID_GRN_SPACE = 'grn.space.'; // スペース

	const STATUS_CREATE = 'create';
	const STATUS_UPDATE = 'update';
	const STATUS_DELETE = 'delete';

	/** @var string */
	public $moduleId;
	/** @var int */
	public $itemId;
	/** @var string */
	public $status;
	/** @var bool */
	public $isHistory;
	/** @var int */
	public $readTime;
	/** @var int */
	public $receiveTime;
	/** @var string */
	public $subject;
	/** @var string */
	public $subjectUrl;
	/** @var string */
	public $abstract;
	/** @var string */
	public $abstractUrl;
	/** @var string */
	public $senderName;
	/** @var int */
	public $senderUserId;
	/** @var bool */
	public $attached;
	/** @var int */
	public $version;
	/** @var int Versionで取得したバージョン (どうも通知自体のバージョンと一致しない様子・・・) */
	public $versionVersion;
}
