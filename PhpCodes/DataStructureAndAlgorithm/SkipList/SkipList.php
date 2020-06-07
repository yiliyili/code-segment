<?php
/**
 * code-segment
 *
 * @author    liu hao<liu546hao@163.com>
 * @copyright liu hao<liu546hao@163.com>
 *
 * 跳跃表实现
 *
 */

class Node
{
    private $id;
    public $value;
    public $level;
    public $forward = [];

    public function __construct($value, $level)
    {
        $this->id = uniqid();
        $this->level = $level;
        $this->value = $value;
        for ($i = 0; $i < $level; $i++) {
            $this->forward[$i] = 0;
        }
    }

    public function getID()
    {
        return $this->id;
    }
}

class SkipList
{
    private $maxLevel;
    public $nodePool = [];
    public $header;//头结点id值

    public function __construct($maxLevel)
    {
        $this->maxLevel = $maxLevel;//最大层级
        $header = new Node(-1, $maxLevel);
        $this->addToNodePool($header->getID(), $header);//初始化插入
        $this->header = $header->getID();
    }

    public function addToNodePool($id, $object)
    {
        $this->nodePool[$id] = $object;
        // echo'插入';
        // var_dump($this->nodePool);
    }

    /**
     * @param $id
     * @return Node
     */
    public function getFromNodePool($id)
    {
        // print_r($this->nodePool);
        return isset($this->nodePool[$id]) ? $this->nodePool[$id] : null;
    }

    public function insert($value)
    {
        $visitTrace = [];

        $count = 0;

        $tmp = $this->getFromNodePool($this->header);//获取node对象

        for ($i = $this->maxLevel - 1; $i >= 0; $i--) {
            // var_dump($tmp->forward);//0
            while ($tmp && $tmp->forward[$i]) {//一开始不进来
                $count++;
                if ($count > 20) {
                    break;
                }
                $forward = $this->getFromNodePool($tmp->forward[$i]);

                if ($forward->value < $value) {
                    $tmp = $forward;
                } else if ($forward->value > $value) {
                    break;
                } else {
                    return false;
                }
            }
            if ($tmp) {
                $visitTrace[$i] = $tmp->getID();//一开始获取头结点id
            }
        }

        $level = $this->randomLevel();
        $newNode = new Node($value, $level);
        $this->addToNodePool($newNode->getID(), $newNode);//插入
// echo $level;
// var_dump($visitTrace);
        for ($i = 0; $i < $level; $i++) {
            $trace = $this->getFromNodePool($visitTrace[$i]);
            $newNode->forward[$i] = $trace->forward[$i];
            $trace->forward[$i] = $newNode->getID();
        }

        return true;
    }

    public function find($value)
    {
        $tmp = $this->getFromNodePool($this->header);

        $count = 0;

        for ($i = $this->maxLevel - 1; $i >= 0; $i--) {
            while ($tmp && $tmp->forward[$i]) {
                $count++;
                if ($count > 20) {
                    break;
                }
                $forward = $this->getFromNodePool($tmp->forward[$i]);
                if ($forward->value < $value) {
                    $tmp = $forward;
                } else if ($forward->value > $value) {
                    break;
                } else {
                    return true;
                }
            }
        }

        return false;
    }
    //分配一个随机的层数
    private function randomLevel()
    {
        $level = 1;

        for ($i = 1; $i < $this->maxLevel; $i++) {
            if (rand(0, 1)) {//随机产生0挥着1
                $level++;
            }
        }


        return $level;
    }
}

$testData = [898888, 300, 234, 123, 333, 456, 23, 99];
// $testData = [898888];
echo '<pre>';
$skipList = new SkipList(3);
foreach ($testData as $value) {
    $skipList->insert($value);
}
 print_r($skipList->nodePool);  //打印的结果看起来有bug,气候看
// $test = [898888, 300, 234, 123, 333, 456, 23, 99, 100, 111];
// $test = [898888];

// foreach ($test as $value) {
//     $result = $skipList->find($value);
//     if (in_array($value, $testData) && in_array($value, $test)) {
//         if ($result) {
//             echo '正确<br>';
//         } else {
//             echo '错误<br>';
//         }
//     } else {
//         if ($result) {
//             echo '错误<br>';
//         } else {
//             echo '正确<br>';
//         }
//     }

// }