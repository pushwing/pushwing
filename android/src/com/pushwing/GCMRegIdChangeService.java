package com.pushwing;

import android.app.AlarmManager;
import android.app.PendingIntent;
import android.app.Service;
import android.content.Intent;
import android.os.Handler;
import android.os.IBinder;
import android.os.SystemClock;

import com.google.android.gcm.GCMRegistrar;

/**
 * @title	: GCM RegId Change Service
 * @author	: Domingo
 * @date	: 2014. 3. 15. 오전 12:19:53
 * @content	:
 */
public class GCMRegIdChangeService extends Service{

	private int COUNT = 1000 * 60 * 60 * 24;		// GCM regId가 변했는지 체크하는 시간(24시간 마다 체크)

	@Override
	public void onCreate() {
		super.onCreate();
		try {

			// 등록된 알람이 있으면 제거
			unregisterRestartAlarm();
		} catch (Exception e) {
			e.printStackTrace();
		}
	}

	@Override
	public int onStartCommand(Intent intent, int flags, int startId) {
		try {
			final Handler handler = new Handler();

			// GCM regId가 변경되었는지 24시간 마다 체크한뒤 변경되었으면 서버로 송신한다.
			Runnable runnable = new Runnable() {

				@Override
				public void run() {

					// GCM ID 값 송수신 후 변경되었으면 저장 (GCMIntentService.java 에서 처리)
					GCMRegistrar.checkDevice(getApplicationContext());
					GCMRegistrar.checkManifest(getApplicationContext());
					GCMRegistrar.register(getApplicationContext(), GCMIntentService.GCM_PROJECT_ID);

					handler.postDelayed(this, COUNT);
				}
			};

			handler.postDelayed(runnable, COUNT);

		} catch (Exception e) {
			e.printStackTrace();
		}
		return START_STICKY;
	}

	@Override
	public IBinder onBind(Intent intent) {
		return null;
	}

	@Override
	public void onDestroy() {
		super.onDestroy();
		try {

			// 서비스 재시작 알람 등록
			registerRestartAlarm();
		} catch (Exception e) {
			e.printStackTrace();
		}
	}

	/**
	 * @title	: 서비스 재시작 알람 생성
	 * @author	: Domingo
	 * @date	: 2014. 3. 16. 오전 11:50:47
	 * @content	:
	 */
	public void registerRestartAlarm() {

		Intent intent = new Intent(this, RestartService.class);
		intent.setAction(RestartService.ACTION_RESTART);
		PendingIntent sender = PendingIntent.getBroadcast(this, 0, intent, 0);
		long firstTime = SystemClock.elapsedRealtime();
		firstTime += 10*1000; // 10초 후에 알람이벤트 발생
		AlarmManager am = (AlarmManager)getSystemService(ALARM_SERVICE);
		am.setRepeating(AlarmManager.ELAPSED_REALTIME_WAKEUP, firstTime, 10*1000, sender);
	}

	/**
	 * @title	: 서비스 재시작 알람 제거
	 * @author	: Domingo
	 * @date	: 2014. 3. 16. 오전 11:51:03
	 * @content	:
	 */
	public void unregisterRestartAlarm() {

		Intent intent = new Intent(this, RestartService.class);
		intent.setAction(RestartService.ACTION_RESTART);
		PendingIntent sender = PendingIntent.getBroadcast(this, 0, intent, 0);
		AlarmManager am = (AlarmManager)getSystemService(ALARM_SERVICE);
		am.cancel(sender);
	}
}
