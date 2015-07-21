package com.pushwing.ui.setting;

import android.app.Activity;
import android.content.Context;
import android.os.Bundle;
import android.os.Vibrator;
import android.util.Log;
import android.view.View;
import android.widget.CheckBox;
import android.widget.CompoundButton;
import android.widget.CompoundButton.OnCheckedChangeListener;
import android.widget.LinearLayout;
import android.widget.ToggleButton;

import com.google.ads.AdRequest;
import com.google.ads.AdSize;
import com.google.ads.AdView;
import com.pushwing.R;
import com.pushwing.biz.BizConfiguration;
import com.pushwing.biz.BizPreference;

/**
 * @title	: 환경설정
 * @author	: Domingo
 * @date	: 2014. 4. 5. 오후 4:10:01
 * @content	:
 */
public class Setting_0101 extends Activity implements OnCheckedChangeListener {

	private ToggleButton mTb_Push;			// 푸시 체크박스
	private CheckBox mCb_Sound;				// 푸시 소리
	private CheckBox mCb_Vibrate;			// 푸시 진동

	// 광고
	private AdView mAdView;					// 애드몹

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		try {
			setContentView(R.layout.setting_0101);

			mTb_Push = (ToggleButton) findViewById(R.id.tb_Push);	// 푸시 토글 버튼
			mCb_Sound = (CheckBox) findViewById(R.id.cb_Sound);		// 푸시 소리
			mCb_Vibrate = (CheckBox) findViewById(R.id.cb_Vibrate);	// 푸시 진동

			// 푸시 노티
			if ("Y".equals(BizPreference.gutGCM_NOTI(this))) {
				mTb_Push.setChecked(true);
				findViewById(R.id.ll_Content).setVisibility(View.VISIBLE);
				findViewById(R.id.ll_Nothing).setVisibility(View.GONE);	
			}else {
				mTb_Push.setChecked(false);
				findViewById(R.id.ll_Content).setVisibility(View.GONE);
				findViewById(R.id.ll_Nothing).setVisibility(View.VISIBLE);				
			}

			// 푸시 소리
			if ("Y".equals(BizPreference.gutGCM_SOUND(this))) {
				mCb_Sound.setChecked(true);
				BizPreference.putGCM_SOUND(getBaseContext(), "Y");
			}else {
				mCb_Sound.setChecked(false);
				BizPreference.putGCM_SOUND(getBaseContext(), "N");				
			}

			// 푸시 진동
			if ("Y".equals(BizPreference.gutGCM_VIBRATE(this))) {
				mCb_Vibrate.setChecked(true);
				BizPreference.putGCM_VIBRATE(getBaseContext(), "Y");
			}else {
				mCb_Vibrate.setChecked(false);
				BizPreference.putGCM_VIBRATE(getBaseContext(), "N");				
			}

			mTb_Push.setOnCheckedChangeListener(this);
			mCb_Sound.setOnCheckedChangeListener(this);
			mCb_Vibrate.setOnCheckedChangeListener(this);

			// adView 만들기
			mAdView = new AdView(this, AdSize.BANNER, BizConfiguration.GOOGLE_ADMOB_ID);	// 배너 크기
			LinearLayout ll_Admob = (LinearLayout) findViewById(R.id.ll_Admob);
			ll_Admob.addView(mAdView);
			mAdView.loadAd(new AdRequest());

		} catch (Exception e) {
			e.printStackTrace();
		}
	}

	@Override
	public void onCheckedChanged(CompoundButton buttonView, boolean isChecked) {
		try {
			Log.v("Dragon", "onCheckedChanged");
			Log.v("Dragon", "isChecked = " + isChecked);

			// 설정시 진동
			Vibrator vibrator = (Vibrator) getSystemService(Context.VIBRATOR_SERVICE);
			vibrator.vibrate(200);

			switch (buttonView.getId()) {

			// 푸시
			case R.id.tb_Push:
				if (isChecked) {
					BizPreference.putGCM_NOTI(getBaseContext(), "Y");
					findViewById(R.id.ll_Content).setVisibility(View.VISIBLE);
					findViewById(R.id.ll_Nothing).setVisibility(View.GONE);					
				}else {
					BizPreference.putGCM_NOTI(getBaseContext(), "N");
					findViewById(R.id.ll_Content).setVisibility(View.GONE);
					findViewById(R.id.ll_Nothing).setVisibility(View.VISIBLE);					
				}
				break;

				// 소리
			case R.id.cb_Sound:
				if (isChecked) {
					BizPreference.putGCM_SOUND(getBaseContext(), "Y");					
				}else {
					BizPreference.putGCM_SOUND(getBaseContext(), "N");
				}
				break;

				// 진동
			case R.id.cb_Vibrate:
				if (isChecked) {
					BizPreference.putGCM_VIBRATE(getBaseContext(), "Y");
				}else {
					BizPreference.putGCM_VIBRATE(getBaseContext(), "N");					
				}
				break;
			}
		} catch (Exception e) {
			e.printStackTrace();
		}
	}

	@Override
	public void onDestroy() {
		try {
			super.onDestroy();
			mAdView.destroy();

		} catch (Exception e) {
			e.printStackTrace();
		}
	}
}
