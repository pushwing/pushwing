package com.pushwing.ui;

import java.util.ArrayList;

import android.app.Activity;
import android.app.AlertDialog;
import android.app.Dialog;
import android.content.ContentValues;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.database.Cursor;
import android.graphics.Paint;
import android.os.Bundle;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.BaseAdapter;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.ProgressBar;
import android.widget.TextView;

import com.google.ads.AdRequest;
import com.google.ads.AdSize;
import com.google.ads.AdView;
import com.pushwing.R;
import com.pushwing.biz.BizConfiguration;
import com.pushwing.biz.BizMessage;
import com.pushwing.comutil.ComUtil;
import com.pushwing.db.DBAdapter;

/**
 * @title	: 메인
 * @author	: Domingo
 * @date	: 2014. 3. 16. 오후 10:05:17
 * @content	:
 */
public class Main_0101 extends Activity implements OnItemClickListener {

	// UI
	private Dialog mDialog;							// 처리중 다이얼로그
	private ListView mLv_Main;						// 메인 리스트

	// DB
	private DBAdapter mDBAdapter;					// DB Adapter
	private ArrayList<PushwingData> mPushwingDatas;	// 푸시윙 데이터
	private PushwingAdapter mPushwingAdapter;		// 푸시윙 아답터

	// 광고
	private AdView mAdView;					        // 애드몹

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.main_0101);

		mLv_Main = (ListView) findViewById(R.id.lv_Main);					// 메인 리스트
		mLv_Main.setOnItemClickListener(this);

		mPushwingDatas = new ArrayList<Main_0101.PushwingData>();			// 푸시윙 데이터리스트
		mPushwingAdapter = new PushwingAdapter(this, mPushwingDatas);		// 푸시윙 아답터
		mLv_Main.setAdapter(mPushwingAdapter);

		setPushWingList();
		setProgress();

		// adView 만들기
		mAdView = new AdView(this, AdSize.BANNER, BizConfiguration.GOOGLE_ADMOB_ID);	// 배너 크기
		LinearLayout ll_Admob = (LinearLayout) findViewById(R.id.ll_Admob);
		ll_Admob.addView(mAdView);
		mAdView.loadAd(new AdRequest());
	}

	@Override
	public void onItemClick(AdapterView<?> adapter, View v, int position, long id) {
		try {
			Intent intent = null;

			switch (v.getId()) {
			case R.id.ll_Item:

				PushwingData data = (PushwingData) v.getTag();

				intent = new Intent(this, Main_0102.class);
				intent.addFlags(Intent.FLAG_ACTIVITY_SINGLE_TOP);
				intent.putExtra(BizConfiguration.ExtrasKey.PUSHWING_SUBJECT, data.subject);
				intent.putExtra(BizConfiguration.ExtrasKey.PUSHWING_ITEM_ID, data.itemId);
				startActivity(intent);
				break;
			}
		} catch (Exception e) {
			e.printStackTrace();
		}
	}

	/**
	 * @title		: 처리중 프로그래스 다이얼로그
	 * @author		: Domingo
	 * @date		: 2014. 1. 19. 오전 11:02:14
	 * @description	:
	 */
	private void setProgress() {
		mDialog = new Dialog(this, R.style.ProgressDialog);
		mDialog.addContentView(
				new ProgressBar(this)
				, new LinearLayout.LayoutParams(LinearLayout.LayoutParams.WRAP_CONTENT, LinearLayout.LayoutParams.WRAP_CONTENT));
		mDialog.setCancelable(true);
		mDialog.setCanceledOnTouchOutside(false);
	}

	/**
	 * @title		: 푸시윙 데이터 리스트
	 * @author		: Domingo
	 * @date		: 2014. 1. 19. 오전 11:02:26
	 * @description	:
	 */
	private void setPushWingList() {
		try {
			// 입출금메시지 테이블 세팅
			mDBAdapter = new DBAdapter(this, DBAdapter.SQL_CREATE_PUSH_WING, DBAdapter.PUSHWING_TABLE_NAME);
			mDBAdapter.open();

			// 데이터 전체 선택
			Cursor cur = mDBAdapter.selectTable();
			int count = cur.getCount();

			// 데이터가 없을 경우(처음 접속시) 푸시윙 소개 글 추가 후 세팅
			if (count == 0) {
				// 0:ITEM_ID, 1:CLIENT_NAME, 2:SUBJECT, 3:DATE
				for (int i = 0; i < 3; i++){
					ContentValues values = new ContentValues();
					values.put(DBAdapter.PushWingColumn.CLIENT_NAME, "PUSHWING");	// 푸시윙 클라이언트명

					switch (i){
					case 0:
						values.put(DBAdapter.PushWingColumn.ITEM_ID, BizConfiguration.DefaultItemID.DEFAULT_ITEM_ID_1);	// 푸시윙 아이템 아이디
						values.put(DBAdapter.PushWingColumn.SUBJECT, BizMessage.MainActivity.DEFAULT_MESSAGE_SUBJECT_1);// 푸시윙 제목
						break;

					case 1:
						values.put(DBAdapter.PushWingColumn.ITEM_ID, BizConfiguration.DefaultItemID.DEFAULT_ITEM_ID_2);	// 푸시윙 아이템 아이디
						values.put(DBAdapter.PushWingColumn.SUBJECT, BizMessage.MainActivity.DEFAULT_MESSAGE_SUBJECT_2);// 푸시윙 제목
						break;

					case 2:
						values.put(DBAdapter.PushWingColumn.ITEM_ID, BizConfiguration.DefaultItemID.DEFAULT_ITEM_ID_3);	// 푸시윙 아이템 아이디
						values.put(DBAdapter.PushWingColumn.SUBJECT, BizMessage.MainActivity.DEFAULT_MESSAGE_SUBJECT_3);// 푸시윙 제목
						break;
					}

					// 현재시간 가져오기
					String time = String.valueOf(System.currentTimeMillis());

					values.put(DBAdapter.PushWingColumn.DATE, time);			// 푸시윙 일자
					mDBAdapter.replaceTable(values);
				}

				// 커서, DB close
				cur.close();
				mDBAdapter.close();

				// 입출금메시지 테이블 세팅
				mDBAdapter = new DBAdapter(this, DBAdapter.SQL_CREATE_PUSH_WING, DBAdapter.PUSHWING_TABLE_NAME);
				mDBAdapter.open();

				// 데이터 전체 선택
				cur = mDBAdapter.selectTable();
				count = cur.getCount();
			}

			cur.moveToFirst();	// 커서를 처음으로
			PushwingData data = null;

			for (int i = 0; i < count; i++) {
				data = new PushwingData();				// 푸시윙 데이터
				data.itemId = cur.getString(BizConfiguration.DBIndex.ITEM_ID);			// 아이템 ID
				data.clientName = cur.getString(BizConfiguration.DBIndex.CLIENT_NAME);	// 클라이언트명
				data.subject = cur.getString(BizConfiguration.DBIndex.SUBJECT);			// 아이템 제목
				data.date = cur.getString(BizConfiguration.DBIndex.DATE);				// 일자

				mPushwingDatas.add(data);				// 푸시윙 데이터 추가
				cur.moveToNext();
			}

			cur.close();
			mDBAdapter.close();
			mPushwingAdapter.notifyDataSetChanged();

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

		ab.setNegativeButton(BizMessage.Com.CANCEL, new DialogInterface.OnClickListener() {
			@Override
			public void onClick(DialogInterface arg0, int arg1) {
			}
		});

		ab.setPositiveButton(BizMessage.Com.CONFIRMATION, new DialogInterface.OnClickListener() {
			@Override
			public void onClick(DialogInterface arg0, int arg1) {
				android.os.Process.killProcess(android.os.Process.myPid());
			}
		});

		return ab.create();
	}

	@Override
	public void onBackPressed() {
		try {
			mDialog = createDialog(BizMessage.MainActivity.FINISH_APP);
			mDialog.show();
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

	/**
	 * @title		: 푸시윙 아답터
	 * @author		: Domingo
	 * @date		: 2014. 1. 19. 오전 11:02:38
	 * @description	:
	 */
	private class PushwingAdapter extends BaseAdapter {

		private Context mContext;
		private ArrayList<PushwingData> mDatas;

		public PushwingAdapter(Context context, ArrayList<PushwingData> pushwingDatas) {
			mContext = context;
			mDatas = pushwingDatas;
		}

		@Override
		public int getCount() {
			return mDatas.size();
		}

		@Override
		public Object getItem(int position) {
			return mDatas.get(position);
		}

		@Override
		public long getItemId(int position) {
			return 0;
		}

		@Override
		public View getView(int position, View v, ViewGroup parent) {
			try {
				if (v == null) {
					v = View.inflate(mContext, R.layout.main_item, null);
				}

				LinearLayout ll_Item = (LinearLayout) v.findViewById(R.id.ll_Item);		// 레이아웃
				TextView tv_Date = (TextView) v.findViewById(R.id.tv_Date);				// 일자
				TextView tv_Title = (TextView) v.findViewById(R.id.tv_Title);			// 타이틀

				PushwingData data = mDatas.get(position);

				// 진하게 bold체
				tv_Title.setPaintFlags(tv_Title.getPaintFlags()
						| Paint.FAKE_BOLD_TEXT_FLAG);

				// 타이틀
				tv_Title.setText(data.subject);

				// yyyy-mm-dd 포멧팅
				String date = ComUtil.setFomatingForTimeStamp(Long.parseLong(data.date));

				// 일자
				tv_Date.setText(date);

				ll_Item.setTag(data);

			} catch (Exception e) {
				e.printStackTrace();
			}

			return v;
		}
	}

	/**
	 * @title		: 푸시윙 데이터
	 * @author		: Domingo
	 * @date		: 2014. 1. 19. 오전 11:02:50
	 * @description	:
	 */
	private class PushwingData {
		public String itemId;		// 푸시윙 아이템 아이디
		public String clientName;	// 푸시윙 클라이언트명
		public String date;			// 푸시윙 일자
		public String subject;		// 푸시윙 아이템 제목
	}
}