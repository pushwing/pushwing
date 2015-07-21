package com.pushwing;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;

/**
 * @title	: 서비스 재시작 브로드캐스트
 * @author	: Domingo
 * @date	: 2014. 3. 16. 오후 1:30:59
 * @content	: 재부팅시 서비스를 GCMRegIdChangeService를 재시작 하기 위한 브로드 캐스트
 */
public class RestartService extends BroadcastReceiver{
	
	public static final String ACTION_RESTART = "ACTION.Restart.GCMRegIdChangeService";
	public static final String ACTION_BOOT_COMPLETED = "android.intent.action.BOOT_COMPLETED";

	@Override
	public void onReceive(Context context, Intent intent) {
		
		if(intent.getAction().equals(ACTION_RESTART)) {
			Intent i = new Intent(context, GCMRegIdChangeService.class);
			context.startService(i);
		}
		
		if(intent.getAction().equals(ACTION_BOOT_COMPLETED)) {
			Intent i = new Intent(context, GCMRegIdChangeService.class);
			context.startService(i);
		}
	}
}
