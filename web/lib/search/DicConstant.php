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
            "アイゼン鉱",
            "妖精の土だんご",
            "ファーデンライト",
            "キルヘンミルク",
            "虹プニの体液",
            "正体不明のタマゴ",
            "ザフロア油",
            "敬虔な信者用お札",
            "忘れ去られた霊樹",
            "魂結いの石"
        );
    }

    public static function getReservedWords() {
        return array (
            "1" => "何がわかる"
        );
    }

    public static function getNoneMessages() {
        return array (
            "わからないよ...",
            "それは知らないです",
            "わからない...なにも",
            "わからないってわかって聞いてる？",
            "...",
            "すごい！全然知らない！",
            "あ。それは大丈夫です。",
            "自分の力で解決すべき問題もあるよ！"
        );
    }

}