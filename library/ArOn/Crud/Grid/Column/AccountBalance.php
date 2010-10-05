
<?php
class ArOn_Crud_Grid_Column_AccountBalance extends ArOn_Crud_Grid_Column {

	public $join_table;

	public $key;

	public $parent_key;

	public $join_name;

	public $parentTable = false;

	function __construct($title, $name /* name in table and base */, $isSort = 0) {
		parent::__construct ( $title, $name, $isSort );
	}

	public function updateCurrentSelect($currentSelect) {
		$this->loadHelper ();
		$attachedSelect = "( SELECT
                                    (
                                    
                                    (
                                    
                                    SELECT IF(sum(amount) IS NULL,0,sum(amount))
                                    FROM finance_postings fp
                                    WHERE fp.dst_accnt_num = fa.account_num
                                    AND fp.status = 'confirmed'
                                    )
                                    -
                                    (
                                    SELECT IF(sum(amount) IS NULL,0,sum(amount))
                                    FROM finance_postings fp
                                    WHERE fp.src_accnt_num = fa.account_num
                                    AND fp.status = 'confirmed'
                                    )
                                    
                                    ) AS balance
                                    
                                    FROM finance_accounts fa
                                    WHERE `fa`.`account_num` = `finance_accounts`.`account_num`
                )";
			
		$currentSelect->from ( null, array ($this->name => $attachedSelect ) );
			
		return $currentSelect;
	}

	public function render($row) {
		$value = $row [$this->name];
			
		if (! empty ( $this->action )) {
			$value = $this->createActionLink ( $value, @ $row [$this->gridTitleField] );
		}
		return $value;
	}
}