/**
 * <pre>
 * GCM 수신부
 *
 * @author       : Dragon
 * @Description  : GCM 수신부
 * @History      : 2013.10.27
 *
 * </pre>
 **/
package com.pushwing;

import java.util.HashMap;
import java.util.Map;

import org.json.JSONException;
import org.json.JSONObject;

import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.ContentValues;
import android.content.Context;
import android.content.Intent;
import android.media.RingtoneManager;
import android.support.v4.app.NotificationCompat;
import android.telephony.TelephonyManager;

import com.android.volley.Request.Method;
import com.android.volley.RequestQueue;
import com.android.volley.Response.ErrorListener;
import com.android.volley.Response.Listener;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.google.android.gcm.GCMBaseIntentService;
import com.pushwing.biz.BizConfiguration;
import com.pushwing.biz.BizMessage;
import com.pushwing.biz.BizPreference;
import com.pushwing.biz.BizTransferURL;
import com.pushwing.db.DBAdapter;
import com.pushwing.ui.Main_0102;

public class GCMIntentService extends GCMBaseIntentService {

	public static String GCM_PROJECT_ID = "758979220024";

	//구글 api 페이지 주소 [https://code.google.com/apis/console/#project:긴 번호]
	//#project: 이후의 숫자가 위의 PROJECT_ID 값에 해당한다

	//public 기본 생성자를 무조건 만들어야 한다.
	public GCMIntentService(){ this(GCM_PROJECT_ID); }

	public GCMIntentService(String project_id) { super(project_id); }

	/** 푸시로 받은 메시지 */
	@Override
	protected void onMessage(Context context, Intent intent) {
		try {

			String item_id 		= "";		// 아이템 id
			String client_name 	= "";		// 클라이언트명
			String subject 		= "";		// 제목
			long timestamp;					// timestamp

			// 아이템 id
			if (intent.hasExtra("item_id")) {
				item_id = intent.getStringExtra("item_id");
			}

			// 클라이언트명
			if (intent.hasExtra("client_name")) {
				client_name = intent.getStringExtra("client_name");
			}

			// 제목
			if (intent.hasExtra("subject")) {
				subject = intent.getStringExtra("subject");
			}

			// timestamp
			//		if (intent.hasExtra("timestamp")) {
			//			timestamp = intent.getStringExtra("timestamp");
			//		}

			// 현재시간 가져오기
			timestamp = System.currentTimeMillis();

			Intent resultIntent = new Intent(this, Main_0102.class);
			resultIntent.putExtra(BizConfiguration.ExtrasKey.PUSHWING_ITEM_ID, item_id);
			resultIntent.putExtra(BizConfiguration.ExtrasKey.PUSHWING_SUBJECT, subject);

			PendingIntent pendingIntent = PendingIntent.getActivity(context, 0, resultIntent, PendingIntent.FLAG_UPDATE_CURRENT);

			// 알림 생성
			NotificationCompat.Builder builder =
					new NotificationCompat.Builder(this)
			.setSmallIcon(R.drawable.icon)
			.setContentTitle(BizMessage.Com.PUSHWING)
			.setContentText(subject)
			.setContentIntent(pendingIntent)
			.setAutoCancel(true); 				                                        // 알림바에서 자동 삭제
			
			// 소리
			if ("Y".equals(BizPreference.gutGCM_SOUND(context))) {
				builder.setSound(RingtoneManager.getDefaultUri(RingtoneManager.TYPE_NOTIFICATION));				
			}
			
			// 진동
			if ("Y".equals(BizPreference.gutGCM_VIBRATE(context))) {
				builder.setVibrate(new long[]{500, 500, 500, 500});
			}

			// 노티 (노티가 해제되어있더라도 푸시윙 테이블에는 세팅됨.)
			if ("Y".equals(BizPreference.gutGCM_NOTI(context))) {
				NotificationManager notificationManager = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);
				notificationManager.notify((int)timestamp, builder.build());				
			}

			// 푸시윙 테이블 세팅
			DBAdapter dBAdapter = new DBAdapter(context, DBAdapter.SQL_CREATE_PUSH_WING, DBAdapter.PUSHWING_TABLE_NAME);
			dBAdapter.open();

			// 0:ITEM_ID, 1:CLIENT_NAME, 2:SUBJECT, 3:DATE
			ContentValues values = new ContentValues();
			values.put(DBAdapter.PushWingColumn.ITEM_ID, item_id);			// 푸시윙 아이템 아이디
			values.put(DBAdapter.PushWingColumn.CLIENT_NAME, client_name);	// 푸시윙 클라이언트명
			values.put(DBAdapter.PushWingColumn.SUBJECT, subject);			// 푸시윙 제목
			values.put(DBAdapter.PushWingColumn.DATE, String.valueOf(timestamp));			// 푸시윙 일자
			dBAdapter.replaceTable(values);
			dBAdapter.close();
		} catch (Exception e) {
			e.printStackTrace();
		}
	}

	/** 에러 발생시 */
	@Override
	protected void onError(Context context, String errorId) {
	}

	/** 단말에서 GCM 서비스 등록 했을 때 등록 id를 받는다 */
	@Override
	protected void onRegistered(final Context context, final String regId) {
		try {

			BizPreference.putGCMId(this, regId);

			RequestQueue requestQueue = Volley.newRequestQueue(this);

			// 전문 정상 수신
			Listener<String> listener = new Listener<String>() {
				@Override
				public void onResponse(String result) {

					try {
						JSONObject jsonObject = new JSONObject(result);

						if (jsonObject.has("message")) {
							if ("success".equals(jsonObject.getString("message"))) {
								// 전문 송신이 성공했을 경우 Y 세팅
								BizPreference.putSuccessCheck(context, "Y");
								return;
							}
						}

						// 전문 송신이 실패했을 경우 N 세팅
						BizPreference.putSuccessCheck(context, "N");

					} catch (JSONException e) {
						e.printStackTrace();
					}
				}
			};

			// 전문 오류
			ErrorListener errorListener = new ErrorListener() {
				@Override
				public void onErrorResponse(VolleyError error) {

					// 전문 송신이 실패했을 경우 N 세팅
					BizPreference.putSuccessCheck(context, "N");
				}
			};

			// 핸드폰번호
			TelephonyManager telemamanger = (TelephonyManager) getSystemService(Context.TELEPHONY_SERVICE);
			if (telemamanger.getLine1Number() != null) {

				// 넥서스5 등 핸드폰 번호를 +82 국가 포맷으로 가져오는 경우 010 포맷으로 변환
				final String number = telemamanger.getLine1Number().replace("+82", "0");

				StringRequest request = new StringRequest(Method.POST,
						BizTransferURL.PUSHWING_JOIN,
						listener,
						errorListener) {

					@Override
					protected Map<String, String> getParams() throws com.android.volley.AuthFailureError {
						Map<String, String> params = new HashMap<String, String>();

						params.put("methods", "html");  // html일 경우 화면 출력, json은 암호화 정상출력
						params.put("hp", number);       // 핸드폰 번호
						params.put("cd", regId);        // GCM 아이디
						params.put("os", "2");          // 1: ios, 2:android
						return params;
					};
				};

				// 전문 송신
				requestQueue.add(request);
			}
		} catch (Exception e) {
			e.printStackTrace();
		}
	}

	/** 단말에서 GCM 서비스 등록 해지를 하면 해지된 등록 id를 받는다 */
	@Override
	protected void onUnregistered(Context context, String regId) {
		try {

		} catch (Exception e) {
			e.printStackTrace();
		}
	}
}