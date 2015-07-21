package com.pushwing.biz;

public class BizConfiguration {

	/**
	 * 푸시윙 DB 조회시 최대 조회 값.
	 */
	public static final String PUSHWING_DB_LIMIT	= "20";
	
	/**
	 * 구글 애드몹 광고 아이디
	 */
	public static final String GOOGLE_ADMOB_ID		= "a152163605e78eb";
	
	public class DBIndex {
		public static final int ITEM_ID = 0;
		public static final int CLIENT_NAME = 1;
		public static final int SUBJECT = 2;
		public static final int DATE = 3;
		
	}
	
	/**
	 * @Title ExtrasKey
	 * @author Ace
	 * intent 이동시 사용되는 extra key
	 */
	public class ExtrasKey{
		
		/**
		 * ITEM_ID
		 */
		public static final String PUSHWING_ITEM_ID		= "ITEM_ID";
		
		/**
		 * SUBJECT
		 */
		public static final String PUSHWING_SUBJECT		= "SUBJECT";
	}

    /**
     * 처음 푸시윙 설치시 기본 아이템 ID 3개
     */
    public class DefaultItemID{

        public static final String DEFAULT_ITEM_ID_1    = "0";
        public static final String DEFAULT_ITEM_ID_2    = "1";
        public static final String DEFAULT_ITEM_ID_3    = "2";
    }
}
