package com.pushwing.db;

import com.pushwing.biz.BizConfiguration;

import android.content.ContentValues;
import android.content.Context;
import android.database.Cursor;
import android.database.SQLException;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteDatabase.CursorFactory;
import android.database.sqlite.SQLiteOpenHelper;

/**
 * 푸시윙 DB
 * @author Ace
 */
public class DBAdapter {
	private DatabaseHelper mHelper;
	private SQLiteDatabase mDb;
	private Context mContext;

	/**
	 * 데이터베이스 이름
	 */
	private static final String DATABASE_NAME = "PushWing.db";

	/**
	 * 데이터베이스 버전
	 */
	private static final int DATABASE_VERSION = 1;

	/**
	 * 테이블 생성 쿼리
	 */
	private static String SQL_TABLE_CREATE;

	/**
	 * 테이블 명
	 */
	private static String TABLE_NAME;

	/**
	 * 푸시윙 테이블명
	 */
	public static String PUSHWING_TABLE_NAME = "PUSHWING_TABLE";

	/**
	 * 푸시윙 테이블 생성 쿼리
	 */
	public static final String SQL_CREATE_PUSH_WING = 
			"CREATE TABLE IF NOT EXISTS " + PUSHWING_TABLE_NAME + " ( " + 
					PushWingColumn.ITEM_ID + " TEXT NOT NULL PRIMARY KEY, " +
					PushWingColumn.CLIENT_NAME + " TEXT NOT NULL, " + 
					PushWingColumn.SUBJECT + " TEXT NOT NULL, " + 
					PushWingColumn.DATE + " TEXT NOT NULL)";	

	private static class DatabaseHelper extends SQLiteOpenHelper {

		public DatabaseHelper(Context context) {
			super(context, DATABASE_NAME, null, DATABASE_VERSION);
		}

		public DatabaseHelper(Context context, String name, CursorFactory factory, int version) {
			super(context, name, factory, version);
		}

		@Override
		public void onCreate(SQLiteDatabase db) {
			db.execSQL(SQL_TABLE_CREATE);
		}

		@Override
		public void onOpen(SQLiteDatabase db) {
			super.onOpen(db);
		}

		@Override
		public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersino) {
			db.execSQL("DROP TABLE IF EXISTS " + TABLE_NAME);
		}
	}

	public DBAdapter(Context ctx, String sql, String tableName) {
		this.mContext = ctx;
		SQL_TABLE_CREATE = sql;
		TABLE_NAME = tableName;
	}

	public DBAdapter open() throws SQLException {
		mHelper = new DatabaseHelper(mContext);
		mDb = mHelper.getWritableDatabase();

		mDb.execSQL(SQL_TABLE_CREATE);
		return this;
	}

	public void close() {
		mHelper.close();
	}

	/**
	 * 테이블 입력
	 * @param values
	 * @return
	 */
	public long insertTable(ContentValues values) {
		return mDb.insert(TABLE_NAME, null, values);
	}

	/**
	 * 테이블 열 바꾸기
	 * @param values
	 * @return
	 */
	public long replaceTable(ContentValues values) {
		return mDb.replace(TABLE_NAME, null, values);
	}

	/**
	 * 테이블 삭제
	 * @param pkColumn
	 * @param pkData
	 * @return
	 */
	public boolean deleteTable(String pkColumn, long pkData) {
		return mDb.delete(TABLE_NAME, pkColumn + "=" + pkData, null) > 0;
	}

	public boolean deleteTable(String pkColumn, String pkData) {
		return mDb.delete(TABLE_NAME, pkColumn + "=" + pkData, null) > 0;
	}

	/**
	 * 테이블 전체삭제
	 * @return
	 */
	public boolean deleteTableAll(){
		return mDb.delete(TABLE_NAME, null, null) > 0;
	}

	/**
	 * 테이블 조회
	 * @return 푸시윙 테이블 데이타 20
	 */
	public Cursor selectTable() {
		return mDb.query(TABLE_NAME, null, null, null, null, null, PushWingColumn.DATE + " desc", BizConfiguration.PUSHWING_DB_LIMIT);
	}

	/**
	 * PushWing 칼럼
	 */
	public static class PushWingColumn{

		/**
		 * 푸시윙 메시지 키
		 */
		public final static String ITEM_ID 	= "ITEM_ID";
		
		/**
		 * 푸시윙 클라이언트명
		 */
		public final static String CLIENT_NAME 	= "CLIENT_NAME";

		/**
		 * 푸시윙 메시지 제목
		 */
		public final static String SUBJECT 	= "SUBJECT";

		/**
		 * 푸시윙 일자
		 */
		public final static String DATE		= "DATE";

		/**
		 * 입출금 테이블 전체 배열
		 */
		public static final String[] All = {
			PushWingColumn.ITEM_ID,
			PushWingColumn.CLIENT_NAME,
			PushWingColumn.SUBJECT,
			PushWingColumn.DATE
		};
	}
}
