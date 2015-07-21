package com.pushwing.biz;

/**
 * @title	: 메시지
 * @author	: Domingo
 * @date	: 2014. 3. 21. 오후 10:07:49
 * @content	: String 값 정의
 */
public class BizMessage {
	
	/**
	 * 공통 안내 메시지
	 */
	private static final String COM_INFO_MESSAGE	= "고품격 푸시 알리미 서비스~!\n푸시윙을 이용해주셔서 감사합니다!!\n\n";

	/**
	 * @title	: 공통 메시지
	 * @author	: Domingo
	 * @date	: 2014. 3. 21. 오후 10:08:13
	 * @content	:
	 */
	public class Com {
		
		/**
		 * Pushwing
		 */
		public static final String PUSHWING			= "Pushwing";
		
		/**
		 * 확인
		 */
		public static final String CONFIRMATION		= "확인";
		
		/**
		 * 취소
		 */
		public static final String CANCEL			= "취소";
		
		/**
		 * 업데이트
		 */
		public static final String UPDATE			= "업데이트";
		
		/**
		 * 나중에
		 */
		public static final String AFTER			= "나중에";
		
		/**
		 * 종료
		 */
		public static final String FINISH			= "종료";
	}
	
	/**
	 * @title	: Intro 화면 메시지
	 * @author	: Domingo
	 * @date	: 2014. 3. 21. 오후 10:48:55
	 * @content	:
	 */
	public class Intro {
		
		/**
		 * 미개통 스마트폰 이용불가
		 */
		public static final String NOT_OPPENED_PHONE	= COM_INFO_MESSAGE + "개통되지 않은 스마트폰으로는 이용하실 수 없습니다.\n개통된 스마트폰으로 이용해주세요.";
		
		/**
		 * 인터넷 미연결
		 */
		public static final String CANNOT_CONECTION_INTERNET	= COM_INFO_MESSAGE + "현재 인터넷이 연결되지 않았습니다.\n인터넷을 연결해주세요.";
		
		/**
		 * 푸시윙 서비스 점검중입니다. 잠시 후 이용해주세요.
		 */
		public static final String CANNOT_CONECTION_GOOGLE	= COM_INFO_MESSAGE + "이용에 불편을 드려 죄송합니다.\n현재 구글 푸시서버가 점검중입니다.\n잠시 후 이용해주세요.";

		/**
		 * 푸시윙 서비스 점검중입니다. 잠시 후 이용해주세요.
		 */
		public static final String CANNOT_CONECTION_PUSHWING	= COM_INFO_MESSAGE + "이용에 불편을 드려 죄송합니다.\n현재 푸시윙 서비스 점검중입니다.\n잠시 후 이용해주세요.";
		
		/**
		 * 업데이트 메시지
		 */
		public static final String UPDATE_CONTENT	= COM_INFO_MESSAGE + "좀 더 나은 서비스를 제공하기 위해 몇 가지 기능을 추가하였습니다.\n\n고객님이 원하시는 서비스를 위해 최선을 다하겠습니다.\n감사합니다. ^ㅡ^\n\n-푸시윙 팀 올림!";		
	}
	
	/**
	 * @title	: 메인 화면 메시지
	 * @author	: Domingo
	 * @date	: 2014. 3. 21. 오후 10:54:48
	 * @content	:
	 */
	public class MainActivity {
		
		/**
		 * 앱 종료 메시지
		 */
		public static final String FINISH_APP	= "푸시윙을 종료하시겠습니까?";

		/**
		 * 기본 메시지 제목 1
		 */
		public static final String DEFAULT_MESSAGE_SUBJECT_1	= "PUSHWING에 사이트를 요청하세요.";
		
		/**
		 * 기본 메시지 제목 2
		 */
		public static final String DEFAULT_MESSAGE_SUBJECT_2	= "PUSHWING에서 지원하는 사이트 입니다.";
		
		/**
		 * 기본 메시지 제목 3
		 */
		public static final String DEFAULT_MESSAGE_SUBJECT_3	= "환영합니다! PUSHWING 입니다.";
	}
	
	/**
	 * @title	: 상세 화면 메시지
	 * @author	: Domingo
	 * @date	: 2014. 3. 21. 오후 10:49:17
	 * @content	:
	 */
	public class SubActivity {
		
		/**
		 * 기본 메시지 1
		 */
		public static final String DEFAULT_MESSAGE_1	= "아래 \"웹사이트 방문\" 버튼을 클릭하여 푸시윙에서 푸시알림을 받고 싶은 커뮤니티를 알려 주세요! \n푸시윙 팀에서 빠르게 추가해 드리겠습니다!";
		
		/**
		 * 기본 메시지 2
		 */
		public static final String DEFAULT_MESSAGE_2	= "아래 \"웹사이트 방문\" 버튼을 클릭하여 푸시윙 사이트를 방문해 보세요!\n푸시윙에서 어떤 사이트를 지원하고 있는지 안내해드립니다!";
		
		/**
		 * 기본 메시지 3
		 */
		public static final String DEFAULT_MESSAGE_3	= "푸시윙은 전용앱이 없는 커뮤니티의 푸시알림을 모두 받아 드려요!\n더 많은 커뮤니티에서 회원님을 위한 푸시알림을 받아 드리기 위해 노력하겠습니다!";
	}
	
}
