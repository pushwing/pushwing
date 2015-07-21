package com.pushwing.biz;

/**
 * 통신 주소
 * @author Ace
 */
public class BizTransferURL {

    private static final String PUSHWING_DOMAIN = "http://eg.pushwing.com/";

    /**
     * 푸시윙 처음 진입시 송신
     * @pram methods	: 데이터 타입 (html, json)
     * @pram hp			: 핸드폰번호
     */
    public static final String PUSHWING_INTRO = PUSHWING_DOMAIN + "go/";

	/**
	 * 푸시윙 회원가입
	 * @pram methods	: html일 경우 화면 출력, json은 암호화 정상출력
	 * @pram hp 		: 핸드폰번호
	 * @pram cd 		: device id
	 * @pram os 		: 1: ios, 2:android
	 */
	public static final String PUSHWING_JOIN = PUSHWING_DOMAIN + "sd/";

	/**
	 * 푸시윙 데이터 가져오기
	 * @pram methods	: 데이터 타입 (html, json)
	 * @pram id			: 푸시 아이템 아이디
	 */
	public static final String PUSHWING_GET_CONTENT = PUSHWING_DOMAIN + "fd/";
}
