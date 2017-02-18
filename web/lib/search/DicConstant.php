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
            "探索"
        );
    }

    public static function getLocationSearchWords() {
        return array (
            "どこ",
            "場所"
        );
    }

    public static function getMaterialWords() {
        return array (
            "魔法の草",
            "コバルト草",
            "妖精の土だんご",
            "妖精の毒草",
            "ミスティックハーブ"
        );
    }

	public static function getPriceWords() {
		return array (
			"巡礼街道",
			"彩花の園",
			"岩地の雑木林",
			"大地の傷痕"
		);
	}

    public static function getReservedWords() {
        return array (
            "1" => array(
                "何ができる",
                "何がわかる",
                "ヘルプ"
            ),
			"2" => array("をもっと詳しく教えて"),
            "3" => array(
                "ソフィー",
                "起きて",
                "おはよう",
                "おはようございます"
            ),
            "4" => array(
                "おやすみ",
                "帰る",
                "バイバイ",
                "さようなら",
                "また明日"
            )
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

}