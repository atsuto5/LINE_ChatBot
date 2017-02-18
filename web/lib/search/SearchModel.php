<?php
/**
 * Created by IntelliJ IDEA.
 * User: haradakazumi
 * Date: 2017/02/12
 * Time: 21:43
 */

require_once ('./lib/search/DicConstant.php');

class SearchModel {

    private $tokenModel;

    private $operation;
    private $materials;
    private $eventType;
    private $searchLimit = 80;
    private $materialLimit = 60;
    private $reservedLimit = 0.7;
    private $reservedMessageKey = false;
    /**
     * SearchModel constructor.
     * @param TokenModel $tokenModel
     * @param string $eventType
     */
    public function __construct($tokenModel,$eventType = null) {
        $this->tokenModel = $tokenModel;
        $this->eventType = $eventType;

        $this->setOperation();
        $this->materials = array();

        error_log($this->operation);
        if ($this->operation == NONE) {
            return;
        } else if ($this->operation == SEARCH) {
            $this->setMaterial();
        } else if ($this->operation == RESERVE) {
            $this->reserveAction();
        }
    }

    private function setOperation() {
        $tokens = $this->tokenModel->getToken();

        if (!is_null($this->eventType)) {
            switch ($this->eventType) {
                case "join":
                    $this->operation = JOIN;
                    return;
                case "postback":
                    $this->operation = POSTBACK;
                    return;
            }
        }

        if ($this->checkReservedWord()) {
            $this->operation = RESERVE;
            return;
        }

        $reverse = array_reverse($tokens);
        foreach ($reverse as $token) {
            error_log(print_r($token,true));
            //operation Search
            foreach (DicConstant::getSearchWords() as $word) {
                $result = 0;
                similar_text($token->surface,$word,$result);
                error_log($token->surface."と".$word."の類似度は".$result);

                if ($result > $this->searchLimit) {
                    $this->operation = SEARCH;
                    return;
                }
            }
        }

        $this->operation = NONE;
        return;
    }

    private function setMaterial() {
		foreach (DicConstant::getMaterialWords() as $word) {
			$result = 0;
			$nounsText = $this->tokenModel->getInVerbsText();
			similar_text($nounsText,$word,$result);

			error_log($nounsText."と".$word."の類似度は".$result);

			if ($result > $this->materialLimit) {
				$this->materials[] =$word;
			}
		}
    }

    private function reserveAction() {
        switch ($this->reservedMessageKey) {
            case HELP:
                break;
            case SEARCH_DETAIL :
                break;
            case WAKEUP :
                break;
            case SLEEP :
                break;
        }
    }

    private function checkReservedWord() {
        foreach (DicConstant::getReservedWords() as $key => $words) {
            foreach ($words as $word) {
                $sim = self::levenshteinNormalizedUtf8($this->tokenModel->getOriginText(),$word);
                error_log($this->tokenModel->getOriginText()."と".$word."のレーベンシュタイン距離：".$sim);

                if ($sim > $this->reservedLimit) {
                    $this->reservedMessageKey = $key;
                    return true;
                }

                //文字列が含まれている場合も予約語判定する。
                if (mb_strpos($this->tokenModel->getOriginText(),$word, 0, "UTF-8") !== false) {
                    $this->reservedMessageKey = $key;
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * @return array
     */
    public function getMaterials()
    {
        return $this->materials;
    }

	/**
	 * @return string
	 */
    public function getReservedMessageKey()
    {
        return $this->reservedMessageKey;
    }

    private static function levenshteinNormalizedUtf8($s1, $s2, $cost_ins = 1, $cost_rep = 1, $cost_del = 1) {
        $l1 = mb_strlen($s1, 'UTF-8');
        $l2 = mb_strlen($s2, 'UTF-8');
        $size = max($l1, $l2);
        if (!$size) {
            return 0;
        }
        if (!$s1) {
            return $l2 / $size;
        }
        if (!$s2) {
            return $s1 / $size;
        }
        return 1.0 - self::levenshteinUtf8($s1, $s2, $cost_ins, $cost_rep, $cost_del) / $size;
    }

    private static function levenshteinUtf8($s1, $s2, $cost_ins = 1, $cost_rep = 1, $cost_del = 1) {
        $s1 = preg_split('//u', $s1, -1, PREG_SPLIT_NO_EMPTY);
        $s2 = preg_split('//u', $s2, -1, PREG_SPLIT_NO_EMPTY);
        $l1 = count($s1);
        $l2 = count($s2);
        if (!$l1) {
            return $l2 * $cost_ins;
        }
        if (!$l2) {
            return $l1 * $cost_del;
        }
        $p1 = array_fill(0, $l2 + 1, 0);
        $p2 = array_fill(0, $l2 + 1, 0);
        for ($i2 = 0; $i2 <= $l2; ++$i2) {
            $p1[$i2] = $i2 * $cost_ins;
        }
        for ($i1 = 0; $i1 < $l1; ++$i1) {
            $p2[0] = $p1[0] + $cost_ins;
            for ($i2 = 0; $i2 < $l2; ++$i2) {
                $c0 = $p1[$i2] + ($s1[$i1] === $s2[$i2] ? 0 : $cost_rep);
                $c1 = $p1[$i2 + 1] + $cost_del;
                if ($c1 < $c0) {
                    $c0 = $c1;
                }
                $c2 = $p2[$i2] + $cost_ins;
                if ($c2 < $c0) {
                    $c0 = $c2;
                }
                $p2[$i2 + 1] = $c0;
            }
            $tmp = $p1;
            $p1 = $p2;
            $p2 = $tmp;
        }
        return $p1[$l2];
    }



}