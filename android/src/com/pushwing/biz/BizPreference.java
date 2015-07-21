package com.pushwing.biz;

import android.app.Activity;
import android.content.Context;
import android.content.SharedPreferences;

public class BizPreference {

	// 프리퍼런스 명
	private static final String PREF_PUSHWING 	    = "PUSHWING";	    	// 푸쉬윙

	// 프리퍼런스 키

	/**
	 * GCM regId
	 */
	private static final String GCMID			    = "GCMID";

	/**
	 * GCM regId 전문 송신이 성공했는지 여부
	 */
	private static final String SUCCESS_CHECK		= "SUCCESS_CHECK";
	
	/**
	 * GCM 알림 노티(N:노티없음, Y:노티있음)
	 */
	private static final String GCM_NOTI      	 	= "GCM_NOTI";

	/**
	 * GCM 알림 소리(N:소리없음, Y:소리있음)
	 */
	private static final String GCM_SOUND       	= "GCM_SOUND";

	/**
	 * GCM 알림 진동(N:진동없음, Y:진동있음)
	 */
	private static final String GCM_VIBRATE       	= "GCM_VIBRATE";

	/**
	 * @title	: getString
	 * @author	: Domingo
	 * @date	: 2014. 3. 16. 오후 1:52:00
	 * @content	: 
	 * @param acvt
	 * @param pkey
	 * @param key
	 * @param def
	 * @return
	 */
	private static String getString(Activity acvt, String pkey, String key, String def) {
		String value = "";

		SharedPreferences pref = acvt.getSharedPreferences(pkey, Activity.MODE_PRIVATE);
		if(pref == null) return value;
		value = pref.getString(key, def);
		return value;
	}

	/**
	 * setString
	 * @param atvt	Activity
	 * @param pkey
	 * @param key
	 * @param val
	 */
	private static void setString(Activity atvt, String pkey, String key, String val) {
		SharedPreferences Pref = atvt.getSharedPreferences(pkey, Activity.MODE_PRIVATE);
		SharedPreferences.Editor editor = Pref.edit();
		editor.putString(key, val);
		editor.commit();
	}

	/**
	 * @title	: getString
	 * @author	: Domingo
	 * @date	: 2014. 3. 16. 오후 1:51:38
	 * @content	: 
	 * @param context
	 * @param pkey
	 * @param key
	 * @param def
	 * @return
	 */
	private static String getString(Context context, String pkey, String key, String def) {
		String value = "";

		SharedPreferences pref = context.getSharedPreferences(pkey, Activity.MODE_PRIVATE);
		if(pref == null) return value;
		value = pref.getString(key, def);
		return value;
	}

	/**
	 * @title	: setString
	 * @author	: Domingo
	 * @date	: 2014. 3. 16. 오후 1:51:25
	 * @content	: 
	 * @param context
	 * @param pkey
	 * @param key
	 * @param val
	 */
	private static void setString(Context context, String pkey, String key, String val) {
		SharedPreferences Pref = context.getSharedPreferences(pkey, Activity.MODE_PRIVATE);
		SharedPreferences.Editor editor = Pref.edit();
		editor.putString(key, val);
		editor.commit();
	}

	/**
	 * @title	: GCM ID
	 * @author	: Domingo
	 * @date	: 2014. 3. 16. 오후 1:51:12
	 * @content	: 
	 * @param context
	 * @param value
	 */
	public static void putGCMId(Context context, String value) {
		setString(context, PREF_PUSHWING, GCMID, value);
	}

	/**
	 * @title	: GCM ID
	 * @author	: Domingo
	 * @date	: 2014. 3. 16. 오후 1:50:59
	 * @content	: 
	 * @param context
	 * @return
	 */
	public static String getGCMId(Context context){
		return getString(context, PREF_PUSHWING, GCMID, "");
	}

	/**
	 * @title	: GCM regId 전문 성공 여부
	 * @author	: Domingo
	 * @date	: 2014. 3. 16. 오후 2:05:33
	 * @content	: 
	 * @param context
	 * @param value
	 */
	public static void putSuccessCheck(Context context, String value){
		setString(context, PREF_PUSHWING, SUCCESS_CHECK, value);
	}

	/**
	 * @title	: GCM regId 전문 성공 여부
	 * @author	: Domingo
	 * @date	: 2014. 3. 16. 오후 2:06:27
	 * @content	: 
	 * @param context
	 * @return SUCCESS_CHECK : Y, N
	 */
	public static String getSuccessCheck(Context context){
		return getString(context, PREF_PUSHWING, SUCCESS_CHECK, "N");
	}
	
	/**
	 * @title	: GCM 노티
	 * @author	: Domingo
	 * @date	: 2014. 4. 6. 오전 1:37:15
	 * @content	: N:노티없음, Y:노티있음
	 * @param context
	 * @param value
	 */
	public static void putGCM_NOTI(Context context, String value){
		setString(context, PREF_PUSHWING, GCM_NOTI, value);
	}
	
	/**
	 * @title	: GCM 노티
	 * @author	: Domingo
	 * @date	: 2014. 4. 6. 오전 1:36:57
	 * @content	: N:노티없음, Y:노티있음
	 * @param context
	 * @return
	 */
	public static String gutGCM_NOTI(Context context){
		return getString(context, PREF_PUSHWING, GCM_NOTI, "Y");      // Default 노티 있음
	}

	/**
	 * @title	: GCM 소리
	 * @author	: Domingo
	 * @date	: 2014. 4. 6. 오전 1:37:15
	 * @content	: N:소리없음, Y:소리있음
	 * @param context
	 * @param value
	 */
	public static void putGCM_SOUND(Context context, String value){
		setString(context, PREF_PUSHWING, GCM_SOUND, value);
	}

	/**
	 * @title	: GCM 소리
	 * @author	: Domingo
	 * @date	: 2014. 4. 6. 오전 1:36:57
	 * @content	: N:소리없음, Y:소리있음
	 * @param context
	 * @return
	 */
	public static String gutGCM_SOUND(Context context){
		return getString(context, PREF_PUSHWING, GCM_SOUND, "Y");      // Default 소리 있음
	}

	/**
	 * @title	: GCM 진동
	 * @author	: Domingo
	 * @date	: 2014. 4. 6. 오전 1:37:15
	 * @content	: N:진동없음, Y:진동있음
	 * @param context
	 * @param value
	 */
	public static void putGCM_VIBRATE(Context context, String value){
		setString(context, PREF_PUSHWING, GCM_VIBRATE, value);
	}

	/**
	 * @title	: GCM 진동
	 * @author	: Domingo
	 * @date	: 2014. 4. 6. 오전 1:36:57
	 * @content	: N:진동없음, Y:진동있음
	 * @param context
	 * @return
	 */
	public static String gutGCM_VIBRATE(Context context){
		return getString(context, PREF_PUSHWING, GCM_VIBRATE, "Y");      // Default 진동 있음
	}
}
