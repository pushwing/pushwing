package com.pushwing.ui;

import java.util.HashMap;
import java.util.Map;

import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.pm.PackageInfo;
import android.content.pm.PackageManager;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.net.Uri;
import android.os.Bundle;
import android.os.Handler;
import android.telephony.TelephonyManager;
import android.util.Log;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.google.android.gcm.GCMRegistrar;
import com.pushwing.GCMIntentService;
import com.pushwing.R;
import com.pushwing.biz.BizConfiguration;
import com.pushwing.biz.BizMessage;
import com.pushwing.biz.BizPreference;
import com.pushwing.biz.BizTransferURL;

/**
 * @title	: Intro
 * @author	: Domingo
 * @date	: 2014. 3. 14. 오후 11:38:17
 * @content	: 1. 인터넷 미연결, 미개통 등 이슈가 있을 경우 앱 종료
 * 			  2. GCM regId를 받아와서 서버로 정상 송신(GCMIntentService) 하는지 최대 5번 체크. 정상처리 되지 않으면 앱 종료.
 * 			  3. GCM regId가 변경 될 수도 있기 때문에 24시간마다 한번씩 체크하는 서비스 생성
 */
public class Intro extends Activity {

	private Dialog mDialog;			// 처리중 다이얼로그
	private int mSendCount	= 5;	// 서버로 송신하는 횟수(5회 초과 후 정상응답 받지 못할 경우 앱 종료)

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.intro);

		// 이슈로부터 앱 종료
		appKillFromIsue();
	}

	/**
	 * @title		: 엑티비티 이동
	 * @author		: Domingo
	 * @date		: 2014. 1. 19. 오후 2:04:33
	 * @description	: 파라미터에 값이 있으면 SubActivity | 없으면 MainActivity
	 */
	public void moveToActivity() {
		try {

			// GCM 메시지를 클릭하고 넘어온 경우 SubActivity로 이동.
			if (getIntent().hasExtra(BizConfiguration.ExtrasKey.PUSHWING_ITEM_ID)) {

				Intent intent = new Intent(this, Main_0102.class);

				intent.putExtra(BizConfiguration.ExtrasKey.PUSHWING_ITEM_ID,
						getIntent().getStringExtra(BizConfiguration.ExtrasKey.PUSHWING_ITEM_ID));
				intent.putExtra(BizConfiguration.ExtrasKey.PUSHWING_SUBJECT,
						getIntent().getStringExtra(BizConfiguration.ExtrasKey.PUSHWING_SUBJECT));

				startActivity(intent);
				finish();

			}else {
				new Handler().postDelayed(new Runnable() {

					@Override
					public void run() {

						// regId 발급이 안되거나 서버로 송신이 실패했을때 5회까지 재송신 후 계속해서 실패하면 앱 종료
						if (mSendCount > 0) {

							// regId 전문 송신에 성공했을경우 메인으로 넘어감.
							if ("Y".equals(BizPreference.getSuccessCheck(getApplicationContext()))) {

								Intent service = new Intent("com.pushwing.GCMRegIdChangeService");
								startService(service);

								Intent intent = new Intent(getApplicationContext(), Main_0101.class);
								startActivity(intent);
								finish();
								return;
							}else {

								// GCM RegId 가져오기
								getGcmRegId();
							}

						}else {
							mDialog = createDialog(BizMessage.Intro.CANNOT_CONECTION_GOOGLE);
							mDialog.show();
						}

						mSendCount--;
					}
				}, 2000);
			}

		} catch (Exception e) {
			e.printStackTrace();
		}
	}

	/**
	 * @title		: 이슈로부터 앱 종료
	 * @author		: Domingo
	 * @date		: 2014. 1. 19. 오전 11:01:17
	 * @description	: 미개통, 인터넷 미연결. 이슈가 생길 수 있는 상황 사전에 차단. (팝업 후 앱 종료)
	 */
	private void appKillFromIsue() {
		try {

			// 미개통 디바이스 앱종료
			TelephonyManager telemamanger = (TelephonyManager)getSystemService(Context.TELEPHONY_SERVICE);
			if(telemamanger.getLine1Number() == null){
				mDialog = createDialog(BizMessage.Intro.NOT_OPPENED_PHONE);
				mDialog.show();
				return;
			}

			// 네트워크 미연결시 앱 종료
			// 네트워크 연결 관리자의 핸들을 얻습니다.
			ConnectivityManager cm = (ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE);

			// 기본 모바일 네트워크 연결자(3G) 관련 정보를 얻습니다.
			NetworkInfo ni = cm.getNetworkInfo(ConnectivityManager.TYPE_MOBILE);
			boolean isMobileConn = ni.isConnected();

			// WiFi 관련 정보를 얻습니다.
			ni = cm.getNetworkInfo(ConnectivityManager.TYPE_WIFI);
			boolean isWifiConn = ni.isConnected();

			// 네트워크 미연결시 앱 종료
			if(!isWifiConn && !isMobileConn) {
				mDialog = createDialog(BizMessage.Intro.CANNOT_CONECTION_INTERNET);
				mDialog.show();
				return;
			}

			RequestQueue requestQueue = Volley.newRequestQueue(this);

			Response.Listener<String> listener = new Response.Listener<String>() {
				@Override
				public void onResponse(String result) {

					try {
						JSONObject jsonObject = new JSONObject(result);
						
						Log.v("Dragon", "result = " + result);

						// 서버 버전 확인
						if (jsonObject.has("and_ver")) {

							// 서버 버전 (콤마 제거)
							int severVersion = Integer.valueOf(jsonObject.getString("and_ver").replace(".", ""));

							// 앱 버전 (콤마 제거)
							PackageInfo i = getApplicationContext().getPackageManager()
									.getPackageInfo(getApplicationContext().getPackageName(), 0);
							int appVersion = Integer.valueOf(i.versionName.replace(".", ""));

							// 현재 설치된 앱 버전이 서버 버전 보다 낮을 경우 업데이트 팝업
							if (severVersion > appVersion) {

								// 강제 업데이트 여부
								if (jsonObject.has("update")) {
									// 강제 업데이트
									if ("1".equals(jsonObject.getString("update"))) {
										mDialog = createUpdateDialog(true, BizMessage.Intro.UPDATE_CONTENT);
										mDialog.show();
									}else {
										mDialog = createUpdateDialog(false, BizMessage.Intro.UPDATE_CONTENT);
										mDialog.show();
									}
									return;
								}
							}
						}

					}catch (JSONException e){
						e.printStackTrace();
					} catch(PackageManager.NameNotFoundException e) {
						e.printStackTrace();
					}

					// GCM regId 가져오기
					getGcmRegId();
				}
			};

			Response.ErrorListener errorListener = new Response.ErrorListener() {
				@Override
				public void onErrorResponse(VolleyError error) {
					mDialog = createDialog(BizMessage.Intro.CANNOT_CONECTION_PUSHWING);
					mDialog.show();
					return;
				}
			};

			StringRequest request = new StringRequest(Request.Method.POST,
					BizTransferURL.PUSHWING_INTRO,
					listener,
					errorListener) {

				@Override
				protected Map<String, String> getParams() throws com.android.volley.AuthFailureError {
					Map<String, String> params = new HashMap<String, String>();

					params.put("methods", "html");		// html일 경우 화면 출력, json은 암호화 정상출력
					return params;
				};
			};

			requestQueue.add(request);

		} catch (Exception e) {
			e.printStackTrace();
		}
	}

	/**
	 * @title	: GCM regId값 가져오기
	 * @author	: Domingo
	 * @date	: 2014. 3. 14. 오후 11:08:24
	 * @content	:
	 */
	private void getGcmRegId(){
		try {

			// GCM ID 값 송수신 후 변경되었으면 저장 (GCMIntentService.java 에서 처리)
			GCMRegistrar.checkDevice(this);
			GCMRegistrar.checkManifest(this);
			GCMRegistrar.register(this, GCMIntentService.GCM_PROJECT_ID);

			moveToActivity();

		} catch (Exception e) {
			e.printStackTrace();
		}
	}

	/**
	 * @title		: 기본 다이얼로그 팝업
	 * @author		: Domingo
	 * @date		: 2014. 1. 19. 오전 11:01:53
	 * @description	:
	 * @param content : 내용
	 * @return
	 */
	private AlertDialog createDialog(String content) {
		AlertDialog.Builder ab = new AlertDialog.Builder(this);
		ab.setTitle(BizMessage.Com.PUSHWING);
		ab.setMessage(content);
		ab.setCancelable(false);
		ab.setIcon(getResources().getDrawable(R.drawable.icon));

		ab.setPositiveButton(BizMessage.Com.CONFIRMATION, new DialogInterface.OnClickListener() {
			@Override
			public void onClick(DialogInterface arg0, int arg1) {
				android.os.Process.killProcess(android.os.Process.myPid());
			}
		});

		return ab.create();
	}

	/**
	 * @title		: 업데이트 다이얼로그 팝업
	 * @author		: Domingo
	 * @date		: 2014. 2. 23. 오전 11:01:53
	 * @description	: 강제업데이트시 무조건 업데이트, 업데이트 버튼 클릭시 플레이스토어 연결
	 * @param compulsion : 강제업데이트 여부
	 * @param content : 내용
	 * @return
	 */
	private AlertDialog createUpdateDialog(boolean compulsion, String content){
		AlertDialog.Builder ab = new AlertDialog.Builder(this);
		ab.setTitle(BizMessage.Com.PUSHWING);
		ab.setMessage(content);
		ab.setCancelable(false);
		ab.setIcon(getResources().getDrawable(R.drawable.icon));

		// 업데이트 버튼 클릭시 플레이스토어로 보내면서 앱 종료
		ab.setPositiveButton(BizMessage.Com.UPDATE, new DialogInterface.OnClickListener() {
			@Override
			public void onClick(DialogInterface arg0, int arg1) {
				Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse("market://details?id=com.pushwing"));
				startActivity(intent);
				android.os.Process.killProcess(android.os.Process.myPid());
			}
		});

		// 강제업데이트시 종료버튼
		if (compulsion){
			ab.setNegativeButton(BizMessage.Com.FINISH, new DialogInterface.OnClickListener() {
				@Override
				public void onClick(DialogInterface arg0, int arg1) {
					mDialog.dismiss();
					finish();
				}
			});

			// 강제업데이트가 아닐경우 나중에 버튼도 존재함
		}else {
			ab.setNegativeButton(BizMessage.Com.AFTER, new DialogInterface.OnClickListener() {
				@Override
				public void onClick(DialogInterface arg0, int arg1) {
					getGcmRegId();
				}
			});
		}

		return ab.create();
	}
}