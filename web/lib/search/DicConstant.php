<?php
/**
 * Created by IntelliJ IDEA.
 * User: haradakazumi
 * Date: 2017/02/12
 * Time: 21:41
 */
class DicConstant {

    public static function getSearchWords() {
        return array (
            "検索",
            "探す",
            "探し",
            "探索",
            "教え",
        );
    }

    public static function getLocationSearchWords() {
        return array (
            "どこ",
            "場所"
        );
    }

    public static function getMaterialWords() {
        $details = json_decode(file_get_contents("./data/material.json"),true);
        return array_keys($details);
    }

	public static function getPlaceWords() {
		return array (
			"巡礼街道",
			"彩花の園",
			"岩地の雑木林",
			"大地の傷痕"
		);
	}

    public static function getReservedWords() {
        return array (
            HELP => array(
                "何ができる",
                "何がわかる",
                "ヘルプ"
            ),
			SEARCH_DETAIL => array("をもっと詳しく教えて"),
            WAKEUP => array(
                "ソフィー",
                "起きて",
                "おはよう",
                "おはようございます"
            ),
            SLEEP => array(
                "おやすみ",
                "帰る",
                "バイバイ",
                "さようなら",
                "また明日"
            ),
            READ_COMMENT => array("のコメントをみる"),
            WRITE_COMMENT => array("のコメントを書く"),
            WRITE_COMPLETE_COMMENT => array("完了"),
        );
    }

    public static function getNoneMessages() {
        return array (
            "わからないよ...",
            "それは知らないです",
            "わからない...なにも",
            "知らないってわかって聞いてる？",
            "...？",
            "すごい！全然知らない！",
            "あ。それは大丈夫です。",
            "自分の力で解決すべき問題もあるよ！",
            "うわー、博識ですね！",
            "知らないよ！！",
            "わからぬ"
        );
    }

    public static function getWakeUpMessages() {
        return array(
            "はい！",
            "はいは〜い",
            "聞いてるよー",
            "どうかした？",
            "ん？"
        );
    }

    public static function getSleepMessages() {
        return array(
            "おやすみ！",
            "寝ま〜す",
            "じゃあねー",
            "またね〜",
            "用済みですか・・・"
        );
    }

}