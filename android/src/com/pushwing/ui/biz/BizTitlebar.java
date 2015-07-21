package com.pushwing.ui.biz;

import android.content.Context;
import android.content.Intent;
import android.util.AttributeSet;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.Button;
import android.widget.LinearLayout;

import com.pushwing.R;
import com.pushwing.ui.setting.Setting_0101;

public class BizTitlebar extends LinearLayout implements OnClickListener {

    public BizTitlebar(Context context, AttributeSet attrs) {
        super(context, attrs);
        try {
            addView(((LayoutInflater) getContext()
                    .getSystemService(Context.LAYOUT_INFLATER_SERVICE))
                    .inflate(R.layout.biz_titlebar, this, false));

            Button btn_Setting = (Button)findViewById(R.id.btn_Setting);

            // 타이틀바 프로퍼티 적용
            String setting = attrs.getAttributeValue(null, "setting");
            if (setting != null){
                if ("true".equals(setting)){
                    btn_Setting.setVisibility(View.VISIBLE);
                }else {
                    btn_Setting.setVisibility(View.GONE);
                }
            }

            // 환경설정
            findViewById(R.id.btn_Setting).setOnClickListener(this);

        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    @Override
    public void onClick(View v) {
        try {
            Intent intent = null;

            switch (v.getId()) {

                // 환경설정
                case R.id.btn_Setting:
                    intent = new Intent(getContext(), Setting_0101.class);
                    intent.addFlags(Intent.FLAG_ACTIVITY_SINGLE_TOP);
                    getContext().startActivity(intent);
                    break;
            }
        } catch (Exception e) {
            e.printStackTrace();
        }
    }
}