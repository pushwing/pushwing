package com.pushwing.ui;

import java.util.HashMap;
import java.util.Map;

import org.json.JSONException;
import org.json.JSONObject;

import android.app.Activity;
import android.app.Dialog;
import android.content.Intent;
import android.graphics.Paint;
import android.net.Uri;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.webkit.URLUtil;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.TextView;

import com.android.volley.Request.Method;
import com.android.volley.RequestQueue;
import com.android.volley.Response.ErrorListener;
import com.android.volley.Response.Listener;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.google.ads.AdRequest;
import com.google.ads.AdSize;
import com.google.ads.AdView;
import com.pushwing.R;
import com.pushwing.biz.BizConfiguration;
import com.pushwing.biz.BizMessage;
import com.pushwing.biz.BizTransferURL;

/**
 * 
 * @title	: 상세 페이지
 * @author	: Domingo
 * @date	: 2013. 11. 17. 오후 3:55:00
 * @content	:
 */
public class Main_0102 extends Activity {

    // UI
    private RequestQueue mRequestQueue;
    private TextView mTv_Content;			// 내용
    private Button mBtn_GoWeb;				// 웹사이트 방문 버튼
    private Dialog mDialog;					// 처리중 다이얼로그

    // 광고
    private AdView mAdView;					// 애드몹

    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        try {
            setContentView(R.layout.main_0102);

            mRequestQueue = Volley.newRequestQueue(this);

            String subject = "";
            String itemId = "";

            // 제목, 아이템 ID
            if (getIntent().hasExtra(BizConfiguration.ExtrasKey.PUSHWING_SUBJECT) &&
                    getIntent().hasExtra(BizConfiguration.ExtrasKey.PUSHWING_ITEM_ID)) {

                // 제목
                subject = getIntent().getStringExtra(BizConfiguration.ExtrasKey.PUSHWING_SUBJECT);

                // 아이템 ID
                itemId = getIntent().getStringExtra(BizConfiguration.ExtrasKey.PUSHWING_ITEM_ID);

                TextView tv_Title = (TextView) findViewById(R.id.tv_Title);	// 제목
                mTv_Content = (TextView) findViewById(R.id.tv_Content);		// 내용
                mBtn_GoWeb = (Button) findViewById(R.id.btn_GoWeb);			// 웹사이트 방문

                // 진하게 bold체
                tv_Title.setPaintFlags(tv_Title.getPaintFlags()
                        | Paint.FAKE_BOLD_TEXT_FLAG);

                tv_Title.setText(subject);

                // 초기 설치시 생성된 기본 아이템 아이디일 경우 서버 송신 하지 않음
                if (itemId.equals(BizConfiguration.DefaultItemID.DEFAULT_ITEM_ID_1)
                        || itemId.equals(BizConfiguration.DefaultItemID.DEFAULT_ITEM_ID_2)
                        || itemId.equals(BizConfiguration.DefaultItemID.DEFAULT_ITEM_ID_3)){
                    setDefaultContent(itemId);
                }else {
                    setProgress();
                    setContent(itemId);
                }

                // adView 만들기
                mAdView = new AdView(this, AdSize.BANNER, BizConfiguration.GOOGLE_ADMOB_ID);	// 배너 크기
                LinearLayout ll_Admob = (LinearLayout) findViewById(R.id.ll_Admob);
                ll_Admob.addView(mAdView);
                mAdView.loadAd(new AdRequest());
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    /**
     * 처리중 프로그래스 다이얼로그
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
     * 초기 앱 설치시 생성된 기본 아이템 내용 세팅
     * @param itemId
     */
    private void setDefaultContent(String itemId) {

        final Uri uriPartners = Uri.parse("http://www.pushwing.com/main/partners");
        final Uri uriRequest = Uri.parse("http://www.pushwing.com/main/partners#request");

        // PUSHWING에 사이트를 요청하세요.
        if (itemId.equals(BizConfiguration.DefaultItemID.DEFAULT_ITEM_ID_1)) {

            mBtn_GoWeb.setVisibility(View.VISIBLE);
            mBtn_GoWeb.setOnClickListener(new OnClickListener() {

                @Override
                public void onClick(View v) {
                    Intent intent = new Intent(Intent.ACTION_VIEW, uriRequest);
                    startActivity(intent);
                }
            });

            mTv_Content.setText(BizMessage.SubActivity.DEFAULT_MESSAGE_1);

            // PUSHWING에서 지원하는 사이트 입니다.
        } else if (itemId.equals(BizConfiguration.DefaultItemID.DEFAULT_ITEM_ID_2)) {

            mBtn_GoWeb.setVisibility(View.VISIBLE);
            mBtn_GoWeb.setOnClickListener(new OnClickListener() {

                @Override
                public void onClick(View v) {
                    Intent intent = new Intent(Intent.ACTION_VIEW, uriPartners);
                    startActivity(intent);
                }
            });

            mTv_Content.setText(BizMessage.SubActivity.DEFAULT_MESSAGE_2);

            // 환영합니다! PUSHWING 입니다.
        } else if (itemId.equals(BizConfiguration.DefaultItemID.DEFAULT_ITEM_ID_3)) {
            mTv_Content.setText(BizMessage.SubActivity.DEFAULT_MESSAGE_3);
        }
    }

    /**
     * 내용 세팅
     * @param itemId
     */
    private void setContent(final String itemId) {
        try {
            mDialog.show();

            Listener<String> listener = new Listener<String>() {
                @Override
                public void onResponse(String result) {
                    try {
                        JSONObject jsonObject = new JSONObject(result);

                        // 내용
                        if (jsonObject.has("contents")) {
                            mTv_Content.setText(jsonObject.getString("contents"));
                        }

                        // URL
                        if (jsonObject.has("url")) {
                            final String url = jsonObject.getString("url");
                            if (URLUtil.isValidUrl(url)) {
                                mBtn_GoWeb.setVisibility(View.VISIBLE);
                                mBtn_GoWeb.setOnClickListener(new OnClickListener() {

                                    @Override
                                    public void onClick(View v) {
                                        Intent intent = new Intent(Intent.ACTION_VIEW, Uri.parse(url));
                                        startActivity(intent);
                                    }
                                });
                            }
                        }

                        mDialog.dismiss();
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                }
            };

            ErrorListener errorListener = new ErrorListener() {
                @Override
                public void onErrorResponse(VolleyError error) {
                    // error handling
                }
            };

            StringRequest myReq = new StringRequest(Method.POST,
                    BizTransferURL.PUSHWING_GET_CONTENT,
                    listener,
                    errorListener) {

                @Override
                protected Map<String, String> getParams() throws com.android.volley.AuthFailureError {
                    Map<String, String> params = new HashMap<String, String>();
                    params.put("methods", "html");
                    params.put("id", itemId);
                    return params;
                };
            };

            mRequestQueue.add(myReq);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    @Override
    public void onBackPressed() {
        try {
            Intent intent = new Intent(this, Main_0101.class);
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
            startActivity(intent);
            finish();
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