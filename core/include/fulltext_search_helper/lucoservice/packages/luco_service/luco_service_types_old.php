<?php
/**
 * Autogenerated by Thrift
 *
 * DO NOT EDIT UNLESS YOU ARE SURE THAT YOU KNOW WHAT YOU ARE DOING
 */
include_once $GLOBALS['THRIFT_ROOT'].'/Thrift.php';


class thrift_FieldMsg {
  static $_TSPEC;

  public $fieldName = null;
  public $valueType = null;
  public $indexType = null;
  public $storeType = null;
  public $boost = null;
  public $value = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'fieldName',
          'type' => TType::STRING,
          ),
        2 => array(
          'var' => 'valueType',
          'type' => TType::I32,
          ),
        3 => array(
          'var' => 'indexType',
          'type' => TType::I32,
          ),
        4 => array(
          'var' => 'storeType',
          'type' => TType::I32,
          ),
        5 => array(
          'var' => 'boost',
          'type' => TType::DOUBLE,
          ),
        6 => array(
          'var' => 'value',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['fieldName'])) {
        $this->fieldName = $vals['fieldName'];
      }
      if (isset($vals['valueType'])) {
        $this->valueType = $vals['valueType'];
      }
      if (isset($vals['indexType'])) {
        $this->indexType = $vals['indexType'];
      }
      if (isset($vals['storeType'])) {
        $this->storeType = $vals['storeType'];
      }
      if (isset($vals['boost'])) {
        $this->boost = $vals['boost'];
      }
      if (isset($vals['value'])) {
        $this->value = $vals['value'];
      }
    }
  }

  public function getName() {
    return 'FieldMsg';
  }

  public function read($input)
  {
    $xfer = 0;
    $fname = null;
    $ftype = 0;
    $fid = 0;
    $xfer += $input->readStructBegin($fname);
    while (true)
    {
      $xfer += $input->readFieldBegin($fname, $ftype, $fid);
      if ($ftype == TType::STOP) {
        break;
      }
      switch ($fid)
      {
        case 1:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->fieldName);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->valueType);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->indexType);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->storeType);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
          if ($ftype == TType::DOUBLE) {
            $xfer += $input->readDouble($this->boost);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 6:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->value);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        default:
          $xfer += $input->skip($ftype);
          break;
      }
      $xfer += $input->readFieldEnd();
    }
    $xfer += $input->readStructEnd();
    return $xfer;
  }

  public function write($output) {
    $xfer = 0;
    $xfer += $output->writeStructBegin('FieldMsg');
    if ($this->fieldName !== null) {
      $xfer += $output->writeFieldBegin('fieldName', TType::STRING, 1);
      $xfer += $output->writeString($this->fieldName);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->valueType !== null) {
      $xfer += $output->writeFieldBegin('valueType', TType::I32, 2);
      $xfer += $output->writeI32($this->valueType);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->indexType !== null) {
      $xfer += $output->writeFieldBegin('indexType', TType::I32, 3);
      $xfer += $output->writeI32($this->indexType);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->storeType !== null) {
      $xfer += $output->writeFieldBegin('storeType', TType::I32, 4);
      $xfer += $output->writeI32($this->storeType);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->boost !== null) {
      $xfer += $output->writeFieldBegin('boost', TType::DOUBLE, 5);
      $xfer += $output->writeDouble($this->boost);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->value !== null) {
      $xfer += $output->writeFieldBegin('value', TType::STRING, 6);
      $xfer += $output->writeString($this->value);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}

class thrift_RowMsg {
  static $_TSPEC;

  public $seqID = null;
  public $fieldMsgs = null;
  public $boost = null;
  public $indexName = null;
  public $isDelete = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'seqID',
          'type' => TType::I64,
          ),
        2 => array(
          'var' => 'fieldMsgs',
          'type' => TType::LST,
          'etype' => TType::STRUCT,
          'elem' => array(
            'type' => TType::STRUCT,
            'class' => 'thrift_FieldMsg',
            ),
          ),
        3 => array(
          'var' => 'boost',
          'type' => TType::DOUBLE,
          ),
        4 => array(
          'var' => 'indexName',
          'type' => TType::STRING,
          ),
        5 => array(
          'var' => 'isDelete',
          'type' => TType::BOOL,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['seqID'])) {
        $this->seqID = $vals['seqID'];
      }
      if (isset($vals['fieldMsgs'])) {
        $this->fieldMsgs = $vals['fieldMsgs'];
      }
      if (isset($vals['boost'])) {
        $this->boost = $vals['boost'];
      }
      if (isset($vals['indexName'])) {
        $this->indexName = $vals['indexName'];
      }
      if (isset($vals['isDelete'])) {
        $this->isDelete = $vals['isDelete'];
      }
    }
  }

  public function getName() {
    return 'RowMsg';
  }

  public function read($input)
  {
    $xfer = 0;
    $fname = null;
    $ftype = 0;
    $fid = 0;
    $xfer += $input->readStructBegin($fname);
    while (true)
    {
      $xfer += $input->readFieldBegin($fname, $ftype, $fid);
      if ($ftype == TType::STOP) {
        break;
      }
      switch ($fid)
      {
        case 1:
          if ($ftype == TType::I64) {
            $xfer += $input->readI64($this->seqID);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::LST) {
            $this->fieldMsgs = array();
            $_size0 = 0;
            $_etype3 = 0;
            $xfer += $input->readListBegin($_etype3, $_size0);
            for ($_i4 = 0; $_i4 < $_size0; ++$_i4)
            {
              $elem5 = null;
              $elem5 = new thrift_FieldMsg();
              $xfer += $elem5->read($input);
              $this->fieldMsgs []= $elem5;
            }
            $xfer += $input->readListEnd();
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::DOUBLE) {
            $xfer += $input->readDouble($this->boost);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->indexName);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
          if ($ftype == TType::BOOL) {
            $xfer += $input->readBool($this->isDelete);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        default:
          $xfer += $input->skip($ftype);
          break;
      }
      $xfer += $input->readFieldEnd();
    }
    $xfer += $input->readStructEnd();
    return $xfer;
  }

  public function write($output) {
    $xfer = 0;
    $xfer += $output->writeStructBegin('RowMsg');
    if ($this->seqID !== null) {
      $xfer += $output->writeFieldBegin('seqID', TType::I64, 1);
      $xfer += $output->writeI64($this->seqID);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->fieldMsgs !== null) {
      if (!is_array($this->fieldMsgs)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('fieldMsgs', TType::LST, 2);
      {
        $output->writeListBegin(TType::STRUCT, count($this->fieldMsgs));
        {
          foreach ($this->fieldMsgs as $iter6)
          {
            $xfer += $iter6->write($output);
          }
        }
        $output->writeListEnd();
      }
      $xfer += $output->writeFieldEnd();
    }
    if ($this->boost !== null) {
      $xfer += $output->writeFieldBegin('boost', TType::DOUBLE, 3);
      $xfer += $output->writeDouble($this->boost);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->indexName !== null) {
      $xfer += $output->writeFieldBegin('indexName', TType::STRING, 4);
      $xfer += $output->writeString($this->indexName);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->isDelete !== null) {
      $xfer += $output->writeFieldBegin('isDelete', TType::BOOL, 5);
      $xfer += $output->writeBool($this->isDelete);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}

class thrift_ResultMsg {
  static $_TSPEC;

  public $total = null;
  public $count = null;
  public $useTime = null;
  public $resultRow = null;
  public $Keyword = null;
  public $str = null;
  public $groupCount = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'total',
          'type' => TType::I32,
          ),
        2 => array(
          'var' => 'count',
          'type' => TType::I32,
          ),
        3 => array(
          'var' => 'useTime',
          'type' => TType::I32,
          ),
        4 => array(
          'var' => 'resultRow',
          'type' => TType::LST,
          'etype' => TType::MAP,
          'elem' => array(
            'type' => TType::MAP,
            'ktype' => TType::STRING,
            'vtype' => TType::STRING,
            'key' => array(
              'type' => TType::STRING,
            ),
            'val' => array(
              'type' => TType::STRING,
              ),
            ),
          ),
        5 => array(
          'var' => 'Keyword',
          'type' => TType::LST,
          'etype' => TType::STRING,
          'elem' => array(
            'type' => TType::STRING,
            ),
          ),
        6 => array(
          'var' => 'str',
          'type' => TType::STRING,
          ),
        7 => array(
          'var' => 'groupCount',
          'type' => TType::MAP,
          'ktype' => TType::STRING,
          'vtype' => TType::MAP,
          'key' => array(
            'type' => TType::STRING,
          ),
          'val' => array(
            'type' => TType::MAP,
            'ktype' => TType::STRING,
            'vtype' => TType::STRING,
            'key' => array(
              'type' => TType::STRING,
            ),
            'val' => array(
              'type' => TType::STRING,
              ),
            ),
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['total'])) {
        $this->total = $vals['total'];
      }
      if (isset($vals['count'])) {
        $this->count = $vals['count'];
      }
      if (isset($vals['useTime'])) {
        $this->useTime = $vals['useTime'];
      }
      if (isset($vals['resultRow'])) {
        $this->resultRow = $vals['resultRow'];
      }
      if (isset($vals['Keyword'])) {
        $this->Keyword = $vals['Keyword'];
      }
      if (isset($vals['str'])) {
        $this->str = $vals['str'];
      }
      if (isset($vals['groupCount'])) {
        $this->groupCount = $vals['groupCount'];
      }
    }
  }

  public function getName() {
    return 'ResultMsg';
  }

  public function read($input)
  {
    $xfer = 0;
    $fname = null;
    $ftype = 0;
    $fid = 0;
    $xfer += $input->readStructBegin($fname);
    while (true)
    {
      $xfer += $input->readFieldBegin($fname, $ftype, $fid);
      if ($ftype == TType::STOP) {
        break;
      }
      switch ($fid)
      {
        case 1:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->total);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->count);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 3:
          if ($ftype == TType::I32) {
            $xfer += $input->readI32($this->useTime);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 4:
          if ($ftype == TType::LST) {
            $this->resultRow = array();
            $_size7 = 0;
            $_etype10 = 0;
            $xfer += $input->readListBegin($_etype10, $_size7);
            for ($_i11 = 0; $_i11 < $_size7; ++$_i11)
            {
              $elem12 = null;
              $elem12 = array();
              $_size13 = 0;
              $_ktype14 = 0;
              $_vtype15 = 0;
              $xfer += $input->readMapBegin($_ktype14, $_vtype15, $_size13);
              for ($_i17 = 0; $_i17 < $_size13; ++$_i17)
              {
                $key18 = '';
                $val19 = '';
                $xfer += $input->readString($key18);
                $xfer += $input->readString($val19);
                $elem12[$key18] = $val19;
              }
              $xfer += $input->readMapEnd();
              $this->resultRow []= $elem12;
            }
            $xfer += $input->readListEnd();
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 5:
          if ($ftype == TType::LST) {
            $this->Keyword = array();
            $_size20 = 0;
            $_etype23 = 0;
            $xfer += $input->readListBegin($_etype23, $_size20);
            for ($_i24 = 0; $_i24 < $_size20; ++$_i24)
            {
              $elem25 = null;
              $xfer += $input->readString($elem25);
              $this->Keyword []= $elem25;
            }
            $xfer += $input->readListEnd();
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 6:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->str);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 7:
          if ($ftype == TType::MAP) {
            $this->groupCount = array();
            $_size26 = 0;
            $_ktype27 = 0;
            $_vtype28 = 0;
            $xfer += $input->readMapBegin($_ktype27, $_vtype28, $_size26);
            for ($_i30 = 0; $_i30 < $_size26; ++$_i30)
            {
              $key31 = '';
              $val32 = array();
              $xfer += $input->readString($key31);
              $val32 = array();
              $_size33 = 0;
              $_ktype34 = 0;
              $_vtype35 = 0;
              $xfer += $input->readMapBegin($_ktype34, $_vtype35, $_size33);
              for ($_i37 = 0; $_i37 < $_size33; ++$_i37)
              {
                $key38 = '';
                $val39 = '';
                $xfer += $input->readString($key38);
                $xfer += $input->readString($val39);
                $val32[$key38] = $val39;
              }
              $xfer += $input->readMapEnd();
              $this->groupCount[$key31] = $val32;
            }
            $xfer += $input->readMapEnd();
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        default:
          $xfer += $input->skip($ftype);
          break;
      }
      $xfer += $input->readFieldEnd();
    }
    $xfer += $input->readStructEnd();
    return $xfer;
  }

  public function write($output) {
    $xfer = 0;
    $xfer += $output->writeStructBegin('ResultMsg');
    if ($this->total !== null) {
      $xfer += $output->writeFieldBegin('total', TType::I32, 1);
      $xfer += $output->writeI32($this->total);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->count !== null) {
      $xfer += $output->writeFieldBegin('count', TType::I32, 2);
      $xfer += $output->writeI32($this->count);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->useTime !== null) {
      $xfer += $output->writeFieldBegin('useTime', TType::I32, 3);
      $xfer += $output->writeI32($this->useTime);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->resultRow !== null) {
      if (!is_array($this->resultRow)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('resultRow', TType::LST, 4);
      {
        $output->writeListBegin(TType::MAP, count($this->resultRow));
        {
          foreach ($this->resultRow as $iter40)
          {
            {
              $output->writeMapBegin(TType::STRING, TType::STRING, count($iter40));
              {
                foreach ($iter40 as $kiter41 => $viter42)
                {
                  $xfer += $output->writeString($kiter41);
                  $xfer += $output->writeString($viter42);
                }
              }
              $output->writeMapEnd();
            }
          }
        }
        $output->writeListEnd();
      }
      $xfer += $output->writeFieldEnd();
    }
    if ($this->Keyword !== null) {
      if (!is_array($this->Keyword)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('Keyword', TType::LST, 5);
      {
        $output->writeListBegin(TType::STRING, count($this->Keyword));
        {
          foreach ($this->Keyword as $iter43)
          {
            $xfer += $output->writeString($iter43);
          }
        }
        $output->writeListEnd();
      }
      $xfer += $output->writeFieldEnd();
    }
    if ($this->str !== null) {
      $xfer += $output->writeFieldBegin('str', TType::STRING, 6);
      $xfer += $output->writeString($this->str);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->groupCount !== null) {
      if (!is_array($this->groupCount)) {
        throw new TProtocolException('Bad type in structure.', TProtocolException::INVALID_DATA);
      }
      $xfer += $output->writeFieldBegin('groupCount', TType::MAP, 7);
      {
        $output->writeMapBegin(TType::STRING, TType::MAP, count($this->groupCount));
        {
          foreach ($this->groupCount as $kiter44 => $viter45)
          {
            $xfer += $output->writeString($kiter44);
            {
              $output->writeMapBegin(TType::STRING, TType::STRING, count($viter45));
              {
                foreach ($viter45 as $kiter46 => $viter47)
                {
                  $xfer += $output->writeString($kiter46);
                  $xfer += $output->writeString($viter47);
                }
              }
              $output->writeMapEnd();
            }
          }
        }
        $output->writeMapEnd();
      }
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}

?>
