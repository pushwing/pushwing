package com.pushwing.comutil;

import java.sql.Timestamp;
import java.util.Calendar;

/**
 * @title		: ComUtil
 * @author		: Domingo
 * @date		: 2014. 1. 19. 오후 4:05:47
 * @description	: 공통 유틸
 */
public class ComUtil {

	/**
	 * @title	: TimeStamp 변환
	 * @author	: Domingo
	 * @date	: 2014. 3. 23. 오후 12:31:16
	 * @content	: 
	 * @param longStamp
	 * @return	yyyy-MM-dd
	 */
    public static String setFomatingForTimeStamp(long longStamp) {
        try{
            Calendar calendar = Calendar.getInstance();

            // longStamp가 10자리일 경우 millis초 단위가 제거된 경우이므로 1000을 곱한다.
            if (String.valueOf(longStamp).length() == 10){
                calendar.setTimeInMillis(longStamp * 1000);
            }

            Timestamp timestamp = new Timestamp(calendar.getTimeInMillis());
            String date = timestamp.toString();

            // yyyy-MM-dd 형식으로 포맷 변환
            if (date.length() > 9){
                date = date.substring(0, 10);
            }

            return date;

        }catch (Exception e) {
            e.printStackTrace();
        }
        return "";
    }
}
