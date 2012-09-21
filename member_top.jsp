<%@ page import="java.net.*,java.io.*,java.util.*,java.sql.*;" contentType="text/html; charset=Shift_JIS"%>
<%@ page buffer="none" autoFlush="true" %>
<%
//**********************************************************************************************
//	BB@Dee サプライヤ会員向けサイト
//		MAKE DATE	2007/04/20	Ver1.00	by	HS
//		EDIT DATE	2007/04/20	Ver1.00	by	HS	初稿
//		EDIT DATE	2007/05/11	Ver1.01	by	HS	文言変更
//		EDIT DATE	2007/05/15	Ver1.02	by	HS	文言変更
//		EDIT DATE	2007/05/24	Ver1.03	by	HS	過去６ケ月表示
//		EDIT DATE	2007/07/13	Ver1.04	by	HS	流通仕入表示なしのところにショップ管理用も追加
//		EDIT DATE	2008/05/10	Ver2.00	by	HS	リニューアル
//		EDIT DATE	2008/06/11	Ver2.01	by	HS	小分類対応
//		EDIT DATE	2008/06/21	Ver2.02	by	HS	デザイン修正
//		EDIT DATE	2008/06/26	Ver2.03 by	HS	1件の時に金額を０にするのをやめる
//		EDIT DATE	2008/06/30	Ver2.04 by	HS	見積＠ＤｅｅのＬＩＮＫ変更
//		EDIT DATE	2008/12/16	Ver2.05 by	HS	リニューアルリンクの追加・左メニューの改修
//		EDIT DATE	2009/01/08	Ver2.06 by	HS	左メニューの改修・最新情報を非表示
//**********************************************************************************************

//**********************************************************************************************
//	INIファイル読み込み
//**********************************************************************************************
	//
	//	内部使用変数定義
	//
	String	INI_userdb_drv		="";
	String	INI_userdb_jdbc		="";
	String	INI_userdb_ip		="";
	String	INI_userdb_port		="";
	String	INI_userdb_db		="";
	String	INI_userdb_id		="";
	String	INI_userdb_passwd	="";

	String	INI_itemdb_drv		="";
	String	INI_itemdb_jdbc		="";
	String	INI_itemdb_ip		="";
	String	INI_itemdb_port		="";
	String	INI_itemdb_db		="";
	String	INI_itemdb_id		="";
	String	INI_itemdb_passwd	="";

	String	INI_url				="";	//	詳細画面へのリンク

	//
	//	読み込み
	//
	BufferedReader objBr=new BufferedReader(new FileReader(application.getRealPath("/WEB-INF/web.ini")),256);
	while(objBr.ready()){
		StringTokenizer objTkn=new StringTokenizer(objBr.readLine(),"=");
		while(objTkn.hasMoreTokens()){
			String strKey = objTkn.nextToken();
			if( strKey.equals("userdb_drv") )	{	INI_userdb_drv 		= objTkn.nextToken();	break;	}
			if( strKey.equals("userdb_jdbc") )	{	INI_userdb_jdbc		= objTkn.nextToken();	break;	}
			if( strKey.equals("userdb_ip") )	{	INI_userdb_ip 		= objTkn.nextToken();	break;	}
			if( strKey.equals("userdb_port") )	{	INI_userdb_port 	= objTkn.nextToken();	break;	}
			if( strKey.equals("userdb_db") ) 	{	INI_userdb_db 		= objTkn.nextToken();	break;	}
			if( strKey.equals("userdb_id") ) 	{	INI_userdb_id 		= objTkn.nextToken();	break;	}
			if( strKey.equals("userdb_passwd") ){	INI_userdb_passwd 	= objTkn.nextToken();	break;	}

			if( strKey.equals("itemdb_drv") )	{	INI_itemdb_drv 		= objTkn.nextToken();	break;	}
			if( strKey.equals("itemdb_jdbc") )	{	INI_itemdb_jdbc		= objTkn.nextToken();	break;	}
			if( strKey.equals("itemdb_ip") )	{	INI_itemdb_ip 		= objTkn.nextToken();	break;	}
			if( strKey.equals("itemdb_port") )	{	INI_itemdb_port 	= objTkn.nextToken();	break;	}
			if( strKey.equals("itemdb_db") ) 	{	INI_itemdb_db 		= objTkn.nextToken();	break;	}
			if( strKey.equals("itemdb_id") ) 	{	INI_itemdb_id 		= objTkn.nextToken();	break;	}
			if( strKey.equals("itemdb_passwd") ){	INI_itemdb_passwd 	= objTkn.nextToken();	break;	}

			if( strKey.equals("url") )			{	INI_url			 	= objTkn.nextToken();	break;	}
		}
	}
	objBr.close();

//**********************************************************************************************
//	JDBCドライバロード
//**********************************************************************************************
    Class.forName(INI_userdb_drv);
    Class.forName(INI_itemdb_drv);

//**********************************************************************************************
//	DB接続
//**********************************************************************************************
	//	UserDB接続
	String		Userdsn = "jdbc:"+INI_userdb_jdbc+"://"+INI_userdb_ip+":"+INI_userdb_port+"/"+INI_userdb_db+"?user="+INI_userdb_id+"&password="+INI_userdb_passwd;
	Connection	db = DriverManager.getConnection(Userdsn);
	Statement	stmt = db.createStatement();
	String		sql = "";
	ResultSet	rs = null;

	//	ItemDB接続
	String		Itemdsn = "jdbc:"+INI_itemdb_jdbc+"://"+INI_itemdb_ip+":"+INI_itemdb_port+"/"+INI_itemdb_db+"?user="+INI_itemdb_id+"&password="+INI_itemdb_passwd;
	Connection	db2 = DriverManager.getConnection(Itemdsn);
	Statement	stmt2 = db2.createStatement();
	String		sql2 = "";
	ResultSet	rs2 = null;

//**********************************************************************************************
//	member_detail.jspからの引数の取得
//		i1				=	全カテゴリの表示位置
//		varDisp6Flg		=	会員登録のタブ位置
//		varDisp6Flg2	=	全カテゴリのタブ位置
//**********************************************************************************************
	String strSelItem1		= request.getParameter("i1");
	if( strSelItem1 == null ) strSelItem1 = "";

	String varDisp6Flg		= request.getParameter("varDisp6Flg");
	String varDisp6Flg2		= request.getParameter("varDisp6Flg2");
	if( varDisp6Flg == null ) varDisp6Flg = "0";		//	初期は最新を表示
	if( varDisp6Flg2 == null ) varDisp6Flg2 = "0";		//	初期は最新を表示

//**********************************************************************************************
//	ログイン済み確認
//**********************************************************************************************
	// セッション変数の取得
	String strLoginid = (String)session.getAttribute("edu.yale.its.tp.cas.client.filter.user");
	String strLoginDate;

	//
	//	日付の取得
	//
	java.util.Date logindate = new java.util.Date();
	int iyy = logindate.getYear();            // 0 = 1900 年
	int imm = logindate.getMonth();           // 0 = 1 月
	int idd = logindate.getDate();
	int ihh = logindate.getHours();
	int imn = logindate.getMinutes();
	String strhh = Integer.toString(ihh);
	if( strhh.length() < 2 ) strhh = "0" + strhh;
	String strmn = Integer.toString(imn);
	if( strmn.length() < 2 ) strmn = "0" + strmn;
	strLoginDate = (iyy+1900) + "年" + (imm+1) + "月" + idd + "日&nbsp;" + strhh + ":" + strmn;

//**********************************************************************************************
//	ブラウザのキャッシュを無効にする。
//		Last-Modified(最終更新日) : 本日
//		Expires(有効期限)         : 過去日(1970/01/01)
//		pragma no-cache           : HTTP1.0仕様に基づく「キャッシュ無効指示」
//		Cache-Control no-cache    : HTTP1.1仕様に基づく「キャッシュ無効指示」
//**********************************************************************************************
	java.util.Calendar objCal1=java.util.Calendar.getInstance();
	java.util.Calendar objCal2=java.util.Calendar.getInstance();
	objCal2.set(2000,0,1,0,0,0);
	response.setDateHeader("Expires",objCal2.getTime().getTime());
	response.setHeader("progma","no-cache");
	response.setHeader("Cache-Control","no-cache");

//**********************************************************************************************
//	会員チェック
//**********************************************************************************************
	//	ログインフラグ
	int		iLogin_flg			=	0;	//	ログインフラグ[0:none,1:OK]
	String	regist_login_id 	=	"";
	String	regist_password 	=	"";
	String	user_name			=	"";
	String	strItemID			=	"";
	String	delete_flag 		=	"";
	String	dum_regist_login_id =	"";
	String	dum_regist_password =	"";
	String	dum_user_name		=	"";


	//	登録カテゴリ
	HashMap objAryUserItemID = new HashMap();

	//==========================================================================================
	//	通常会員のチェック
	//==========================================================================================
	if( iLogin_flg != 1 ) {
		//	レコードセット接続
		sql = "SELECT v_supplier.login_id,v_supplier.password,v_supplier.person_name,v_sup_request_item.delete_flag,v_sup_request_item.item_id FROM v_supplier LEFT OUTER JOIN v_sup_request_item ON v_supplier.sup_user_id = v_sup_request_item.sup_id where v_supplier.login_id = '"+strLoginid+"'";
		rs = stmt.executeQuery(sql);

		//	読み込み
		while(rs.next()){
			dum_regist_login_id = rs.getString("login_id");		//	LOGIN IDを取得
			dum_regist_password = rs.getString("password");		//	PASSWORDを取得
			dum_user_name	   = rs.getString("person_name");		//	担当者名を取得

			if( dum_regist_login_id.equals(strLoginid) ) {
				//	LOGIN OK
				iLogin_flg = 1;

				regist_login_id	=	dum_regist_login_id;	//	LOGIN IDを取得
				regist_password =	dum_regist_password;	//	PASSWORDを取得
				user_name		=	dum_user_name;			//	担当者名を取得

				delete_flag 	= rs.getString("delete_flag");			//	削除フラグを取得
				if( delete_flag == null || delete_flag.equals("0") ) {
					strItemID		= rs.getString("item_id");				//	登録カテゴリ
					if( strItemID != null ) {
						if( strItemID.length() == 9 ) {
							objAryUserItemID.put(strItemID,"1");			//	小分類しか入れない
						}
					}
				}
			}
		}

		//	レコードセット切断
		rs.close();
	}

//**********************************************************************************************
//	HTML表示
//**********************************************************************************************
	//**********************************************************************************************
	//	全分類取得
	//**********************************************************************************************
	String[] strAryCategory		=	new String[2000];	//	カテゴリ名称
	String[] strAryCategoryID	=	new String[2000];	//	カテゴリID
	HashMap objAryShoItemName	=	new HashMap();		//	小分類名
	int	iCategoryMax			= 0;

	//	レコードセット接続
	sql = "SELECT item_id,item_name FROM v_item order by item_id";
	rs = stmt.executeQuery(sql);

	//	読み込み
	iCategoryMax=0;
	while(rs.next()){
		strAryCategoryID[iCategoryMax]	= rs.getString("item_id");		// ITEMIDを取得
		if( strAryCategoryID[iCategoryMax].length() <= 6 ) {
			//	大分類・中分類のみ抽出
			strAryCategory[iCategoryMax]	= rs.getString("item_name");	// ITEM名称を取得
			iCategoryMax++;
			if( iCategoryMax > 2000 ) break;
		} else {
			//	小分類抽出
			objAryShoItemName.put(rs.getString("item_id"),rs.getString("item_name"));			//	小分類しか入れない

			//	小分類も抽出
			strAryCategory[iCategoryMax]	= rs.getString("item_name");	// ITEM名称を取得
			iCategoryMax++;
			if( iCategoryMax > 2000 ) break;
		}
	}

	//**********************************************************************************************
	//	全分類集計
	//		集計速度を上げるためHASH使用に変更。by SSK
	//**********************************************************************************************
	//	最新情報
	HashMap objAryTotal=new HashMap();
	HashMap objAryCount=new HashMap();
	Hashtable objAryRemainder=new Hashtable();	//	大中分類用の１件データ金額
	//	過去６ヶ月
	HashMap objAryTotal6=new HashMap();
	HashMap objAryCount6=new HashMap();
	Hashtable objAryRemainder6=new Hashtable();	//	大中分類用の１件データ金額

	//==========================================================================================
	//	抽出条件
	//==========================================================================================
	//
	//	現在日時取得
	//
	java.util.Date nwdate = new java.util.Date();
	java.util.Date bfdate = new java.util.Date();

	bfdate.setTime((bfdate.getTime() - (20 * 24 * 3600 * 1000)));
	bfdate.setTime((bfdate.getTime() - (20 * 24 * 3600 * 1000)));
	bfdate.setTime((bfdate.getTime() - (20 * 24 * 3600 * 1000)));
	bfdate.setTime((bfdate.getTime() - (20 * 24 * 3600 * 1000)));
	bfdate.setTime((bfdate.getTime() - (20 * 24 * 3600 * 1000)));
	bfdate.setTime((bfdate.getTime() - (20 * 24 * 3600 * 1000)));
	bfdate.setTime((bfdate.getTime() - (20 * 24 * 3600 * 1000)));
	bfdate.setTime((bfdate.getTime() - (20 * 24 * 3600 * 1000)));
	bfdate.setTime((bfdate.getTime() - (20 * 24 * 3600 * 1000)));

	//
	//	WHERE条件
	//
	String	strWhere = "";
	String	strWhere6 = "";
//	strWhere  += " WHERE prop_entry_limit_date>now() and request_state<>3 and market_id<>10 and (buy_admin_state=1 or buy_admin_state=4 or buy_admin_state=5 or buy_admin_state=11 or buy_admin_state=84 or buy_admin_state=85) ";
//	strWhere6 += " WHERE prop_entry_limit_date>=(timestamp '"+bfdate.toLocaleString()+"') and prop_entry_limit_date<=(timestamp '"+nwdate.toLocaleString()+"') and market_id<>10 and (buy_admin_state=6 or buy_admin_state=7 or buy_admin_state=10 or buy_admin_state=13 or buy_admin_state=91) ";

	strWhere  += " WHERE prop_entry_limit_date>now() and request_state<>3 and (buy_admin_state=1 or buy_admin_state=4 or buy_admin_state=5 or buy_admin_state=11 or buy_admin_state=84 or buy_admin_state=85) ";
	strWhere6 += " WHERE prop_entry_limit_date>=(timestamp '"+bfdate.toLocaleString()+"') and request_state<>1 and prop_entry_limit_date<=(timestamp '"+nwdate.toLocaleString()+"') and (buy_admin_state<>7 and buy_admin_state<>8 and  buy_admin_state<>13) ";

	//==========================================================================================
	//	小分類の集計
	//==========================================================================================
	sql2 = "SELECT item3,sum(sbb_price) AS total,count(item3) AS CNT FROM v_tab_request "+strWhere+" GROUP BY item3 ORDER BY item3";
	rs2 = stmt2.executeQuery(sql2);
	while(rs2.next()){
		String strItem3 = rs2.getString("item3");
		String strTotl3 = rs2.getString("total");
		String strCont3 = rs2.getString("CNT");
		objAryCount.put(strItem3,strCont3);	// 件数

		//	件数の数値化
		int	iCont3 = 0;
		try {
			iCont3 = Integer.parseInt(strCont3);
		} catch(Exception e) {
		}

		//	金額の数値化
		int	iTotl3 = 0;
		try {
			iTotl3 = Integer.parseInt(strTotl3);
		} catch(Exception e) {
		}

/*	1件の時に金額を０にするのをやめる
		if( iCont3 == 1 ) {
			//	中分類の集計
			int iRemainderMid = 0;
			String strRemainderMid = "";
			try {
				strRemainderMid = (String)objAryRemainder.get(strItem3.substring(0,6));
				if( strRemainderMid != null && !strRemainderMid.equals("") ) {
					iRemainderMid = Integer.parseInt(strRemainderMid);
				}
			} catch(Exception e) {
			}
			iRemainderMid += iTotl3;
			strRemainderMid = Integer.toString(iRemainderMid);
			objAryRemainder.put(strItem3.substring(0,6),strRemainderMid);

			//	大分類の集計
			int iRemainderBig = 0;
			String strRemainderBig = "";
			try {
				strRemainderBig = (String)objAryRemainder.get(strItem3.substring(0,3));
				if( strRemainderBig != null && !strRemainderBig.equals("") ) {
					iRemainderBig = Integer.parseInt(strRemainderBig);
				}
			} catch(Exception e) {
			}
			iRemainderBig += iTotl3;
			strRemainderBig = Integer.toString(iRemainderBig);
			objAryRemainder.put(strItem3.substring(0,3),strRemainderBig);

			objAryTotal.put(strItem3,"0");	// 金額
		} else {
*/
			objAryTotal.put(strItem3,strTotl3);	// 金額
/*
		}
*/
	}
	rs2.close();

	//==========================================================================================
	//	中分類の集計
	//==========================================================================================
	sql2 = "SELECT item2,sum(sbb_price) AS total,count(item2) AS CNT FROM v_tab_request "+strWhere+" GROUP BY item2 ORDER BY item2";
	rs2 = stmt2.executeQuery(sql2);
	while(rs2.next()){
		String strItem2 = rs2.getString("item2");
		String strTotl2 = rs2.getString("total");

		int iRemainderMid = 0;
		String strRemainderMid = "";
		try {
			strRemainderMid = (String)objAryRemainder.get(strItem2);
			if( strRemainderMid != null && !strRemainderMid.equals("") ) {
				iRemainderMid = Integer.parseInt(strRemainderMid);
			}
		} catch(Exception e) {
		}
		try {
			if( strTotl2 != null && !strTotl2.equals("") ) {
				iRemainderMid = Integer.parseInt(strTotl2) - iRemainderMid;
			}
		} catch(Exception e) {
		}
		strRemainderMid = Integer.toString(iRemainderMid);

		objAryTotal.put(strItem2,strRemainderMid);		// 金額
		objAryCount.put(strItem2,rs2.getString("CNT"));	// 件数
	}
	rs2.close();

	//==========================================================================================
	//	大分類の集計
	//==========================================================================================
	sql2 = "SELECT item1,sum(sbb_price) AS total,count(item1) AS CNT FROM v_tab_request "+strWhere+" GROUP BY item1 ORDER BY item1";
	rs2 = stmt2.executeQuery(sql2);
	while(rs2.next()){
		String strItem1 = rs2.getString("item1");
		String strTotl1 = rs2.getString("total");

		int iRemainderBig = 0;
		String strRemainderBig = "";
		try {
			strRemainderBig = (String)objAryRemainder.get(strItem1);
			if( strRemainderBig != null && !strRemainderBig.equals("") ) {
				iRemainderBig = Integer.parseInt(strRemainderBig);
			}
		} catch(Exception e) {
		}
		try {
			if( strTotl1 != null && !strTotl1.equals("") ) {
				iRemainderBig = Integer.parseInt(strTotl1) - iRemainderBig;
			}
		} catch(Exception e) {
		}
		strRemainderBig = Integer.toString(iRemainderBig);

		objAryTotal.put(strItem1,strRemainderBig);		// 金額
		objAryCount.put(strItem1,rs2.getString("CNT"));	// 件数
	}
	rs2.close();

	//==========================================================================================
	//	過去６ヶ月小分類の集計
	//==========================================================================================
	sql2 = "SELECT item3,sum(sbb_price) AS total,count(item3) AS CNT FROM v_tab_request "+strWhere6+" GROUP BY item3 ORDER BY item3";
	rs2 = stmt2.executeQuery(sql2);

	while(rs2.next()){
		String strItem3 = rs2.getString("item3");
		String strTotl3 = rs2.getString("total");
		String strCont3 = rs2.getString("CNT");
		objAryCount6.put(strItem3,strCont3);	// 件数
		objAryTotal6.put(strItem3,strTotl3);	// 金額
	}
	rs2.close();

	//==========================================================================================
	//	過去６ヶ月中分類の集計
	//==========================================================================================
	sql2 = "SELECT item2,sum(sbb_price) AS total,count(item2) AS CNT FROM v_tab_request "+strWhere6+" GROUP BY item2 ORDER BY item2";
	rs2 = stmt2.executeQuery(sql2);
	while(rs2.next()){
		String strItem2 = rs2.getString("item2");
		objAryTotal6.put(strItem2,rs2.getString("total"));	// 金額
		objAryCount6.put(strItem2,rs2.getString("CNT"));	// 件数
	}
	rs2.close();

	//==========================================================================================
	//	過去６ヶ月大分類の集計
	//==========================================================================================
	sql2 = "SELECT item1,sum(sbb_price) AS total,count(item1) AS CNT FROM v_tab_request "+strWhere6+" GROUP BY item1 ORDER BY item1";
	rs2 = stmt2.executeQuery(sql2);
	while(rs2.next()){
		String strItem1 = rs2.getString("item1");
		String strTotl1 = rs2.getString("total");
		objAryTotal6.put(strItem1,rs2.getString("total"));	// 金額
		objAryCount6.put(strItem1,rs2.getString("CNT"));	// 件数
	}
	rs2.close();

//**********************************************************************************************
//	ログイン成功時
//**********************************************************************************************
%>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS" />
		<title>Biz@Dee  --DeeCorp--</title>
		<meta name="keywords" content="ソフトバンクグループ,DeeCorp,見積@Dee,リバースオークション,コスト削減,見積,調達,経費削減,マネージメント,内部統制,購買,SOX法,業務効率,経費削減事例,購買管理,管理購買,購買心理,コスト削減事例,業務改善,購買業務,グリーン調達,購買意欲,ネット購買,購買管理規程,購買管理規定,社内コスト削減,電子入札,適正価格,バイヤー,サプライヤー,サプライヤ">
		<meta name="description" content="ディーコープ株式会社">
		<link href="../css/common.css" rel="stylesheet" type="text/css" media="screen,print" />
		<script language="JavaScript" src="js/common.js" type="text/javascript"></script>
		<script language="JavaScript">
		<!--//
				//**********************************************************
				//	グローバル変数定義
				//**********************************************************
				var strAryCategory		= new Array();
				var strAryCategoryCOL	= new Array();
				var	strAryCategoryID	= new Array();
				var	strAryCategoryFLG	= new Array();
				var objAryTotal 		= new Object();  		// Objectの生成
				var objAryCount 		= new Object();  		// Objectの生成
				var objAryTotal6 		= new Object();  		// Objectの生成
				var objAryCount6 		= new Object();  		// Objectの生成
				var objAryUserItemID	= new Object();  		// Objectの生成
				var	objAryShoItemName	= new Object();  		// Objectの生成
				var	iiCategoryMax;
				var	varstrSelItem1 		= "<%=strSelItem1%>";	//	member_detail引数
				var	varDisp6Flg			= <%=varDisp6Flg%>;		//	ユーザー	最新・６ケ月データ切替フラグ[0:最新,1:過去]
				var	varDisp6Flg2		= <%=varDisp6Flg2%>;	//	全カテゴリ	最新・６ケ月データ切替フラグ[0:最新,1:過去]
				var	objAryShoItemNo1	= new Object();  	// Objectの生成
				var	objAryShoItemNo2	= new Object();  	// Objectの生成
				var	objAryShoItemNo3	= new Object();  	// Objectの生成

				//**********************************************************
				//	漢字表記
				//**********************************************************
				function funkJPNYEN2(mony){
					if( Number(mony) == 0 ) return "0万";
					if( Number(mony) < 10000 ) return funkJPNYEN(mony);
					var ii	=	0;
				    var s = "" + mony; // 確実に文字列型に変換する.
				    var p = s.indexOf("."); // 小数点の位置を0オリジンで求める。
				    if (p < 0) { // 小数点が見つからなかった時
				        p = s.length; // 仮想的な小数点の位置とする
				    }
				    var r = s.substring(p, s.length); // 小数点の桁と小数点より右側の文字列。
				    for (var i = 0; i < p; i++) { // (10 ^ i) の位について
				        var c = s.substring(p - 1 - i, p - 1 - i + 1); // (10 ^ i) の位のひとつの桁の数字
				        if (c < "0" || c > "9") { // 数字以外のもの(符合など)が見つかった
				            r = s.substring(0, p - i) + r; // 残りを全部付加する
				            break;
				        }
				        if(i == 4){
							r = "万"; //+ r;

						}
				        if(i == 7){
							r = ","+ r;

						}
				        if(i == 8){
							r = "億" + r; 
						}
				         if (i > 8 && (i-8) % 3 == 0) { // 3 桁ごと、ただし初回は除く
				            r = "," + r; // カンマを付加する
				        }
				       r = c + r; // 数字を一桁追加する。
				 
				   }
				//	alert(r);
				    return r;
				}
				function funkJPNYEN(mony){
					var ii	=	0;
				    var s = "" + mony; // 確実に文字列型に変換する。例では "95839285734.3245"
				    var p = s.indexOf("."); // 小数点の位置を0オリジンで求める。例では 11
				    if (p < 0) { // 小数点が見つからなかった時
				        p = s.length; // 仮想的な小数点の位置とする
				    }
				    var r = s.substring(p, s.length); // 小数点の桁と小数点より右側の文字列。例では ".3245"
				    for (var i = 0; i < p; i++) { // (10 ^ i) の位について
				        var c = s.substring(p - 1 - i, p - 1 - i + 1); // (10 ^ i) の位のひとつの桁の数字。例では "4", "3", "7", "5", "8", "2", "9", "3", "8", "5", "9" の順になる。
				        if (c < "0" || c > "9") { // 数字以外のもの(符合など)が見つかった
				            r = s.substring(0, p - i) + r; // 残りを全部付加する
				            break;
				        }
				        if(i == 4){
							r = "万";//+ r;

						}
				        if(i == 8){
							r = "億" + r; 
						}
				        r = c + r; // 数字を一桁追加する。
				    }
					////alert(r);
				    return r; // 例では "95,839,285,734.3245"
				}

				//**********************************************************
				// (すべての変数に格納する値は0オリジンとする) 
				//**********************************************************
				function myFormatNumber(x) { // 引数の例としては 95839285734.3245
				    var s = "" + x; // 確実に文字列型に変換する。例では "95839285734.3245"
				    var p = s.indexOf("."); // 小数点の位置を0オリジンで求める。例では 11
				    if (p < 0) { // 小数点が見つからなかった時
				        p = s.length; // 仮想的な小数点の位置とする
				    }
				    var r = s.substring(p, s.length); // 小数点の桁と小数点より右側の文字列。例では ".3245"
				    for (var i = 0; i < p; i++) { // (10 ^ i) の位について
				        var c = s.substring(p - 1 - i, p - 1 - i + 1); // (10 ^ i) の位のひとつの桁の数字。例では "4", "3", "7", "5", "8", "2", "9", "3", "8", "5", "9" の順になる。
				        if (c < "0" || c > "9") { // 数字以外のもの(符合など)が見つかった
				            r = s.substring(0, p - i) + r; // 残りを全部付加する
				            break;
				        }
				        if (i > 0 && i % 3 == 0) { // 3 桁ごと、ただし初回は除く
				            r = "," + r; // カンマを付加する
				        }
				        r = c + r; // 数字を一桁追加する。
				    }
				    return r; // 例では "95,839,285,734.3245"
				}

				//**********************************************************
				//	ITEMNOの表示フラグをセットする
				//		input	item_no =	大項目No
				//				flg		=	[0:閉じる,1:開く]
				//**********************************************************
				function setFlg(item_no,flg) {
					var ii;

					//
					for( ii=0; ii<iiCategoryMax; ii++ ) {
						if( strAryCategoryID[ii] == item_no ) {
							strAryCategoryFLG[ii] = flg;
							break;
						}
					}

					//
					//	戻り値
					//
					return ii;
				}

				//**********************************************************
				//	ITEMNOの表示フラグを返す
				//		input	item_no =	大項目No
				//**********************************************************
				function getFlg(item_no) {
					var ii;
					var	iRet;

					//
					for( ii=0; ii<iiCategoryMax; ii++ ) {
						if( strAryCategoryID[ii] == item_no ) {
							iRet = strAryCategoryFLG[ii];
							break;
						}
					}

					//
					//	戻り値
					//
					return iRet;
				}

				//**********************************************************
				//	ユーザー登録カテゴリ表示
				//**********************************************************
				function setUserCategory() {
					//
					//	内部使用変数定義
					//
					var	strCatTable = "";
					var ii;
					var iiMax=0;
					var jjMax=0;
					var	varTotal=0;
					var	varCount=0;
					var numAryDispList	= new Array();	//	表示順

					//
					//	登録カテゴリの件数を見る
					//
					jjMax=0;
					for(var jj in objAryUserItemID) {
						//	表示順を決める為のインデックス作成
						numAryDispList[jjMax] = jj;

						jjMax++;
						//	登録カテゴリに該当した分類に色をつける前作業
						for( ii=0; ii<iiCategoryMax; ii++ ) {
							if( strAryCategoryID[ii].substr(0,strAryCategoryID[ii].length) == jj.substr(0,strAryCategoryID[ii].length) ) {
								strAryCategoryCOL[ii] = 1;	//	色付
							}
						}
					}

					//	ソートする
					for( var nn=0; nn<(jjMax-1); nn++ ) {
						for( var kk=nn+1; kk<jjMax; kk++ ) {
							if( varDisp6Flg==0 ) {
								//	最新
								var varA = Number(objAryTotal[numAryDispList[nn]]);
								var varB = Number(objAryTotal[numAryDispList[kk]]);
							} else {
								//	６ヶ月
								var varA = Number(objAryTotal6[numAryDispList[nn]]);
								var varB = Number(objAryTotal6[numAryDispList[kk]]);
							}
							if(isNaN(varA))  varA = Number(0);
							if(isNaN(varB))  varB = Number(0);
							////alert("nn="+nn+",kk="+kk+",cmp("+varA+"["+numAryDispList[nn]+"],"+varB+"["+numAryDispList[kk]+"])");
							if( varA < varB ) {
								var varDum = numAryDispList[nn];
								numAryDispList[nn] = numAryDispList[kk];
								numAryDispList[kk] = varDum;
							}
						}
					}

					var addMsg = "";
					if( jjMax <= 0 ) {
						//
						//	登録カテゴリなし
						//
						strCatTable += "<h2 class='h2_m_top01'>My案件最新情報　あたなの登録済カテゴリーの案件情報をお届けいたします。</h2>";
						////strCatTable += "<table class='tb-cateList'>";
						strCatTable += "<div class='memTop-cate-error'>";
						strCatTable += "<p class='notice_normal'>現在、ご登録いただいているカテゴリーはありません。</p>";
						strCatTable += "<p>&nbsp;</p>";
						strCatTable += "<p>ご登録いただいた品目カテゴリーにマッチした案件をお届けするサービスとなっておりますので<br />";
						strCatTable += "品目カテゴリーが未登録の場合 案件に関する具体的なご案内をお届けすることができません。</p>";

						////strCatTable += "<p class='mail-ad'>品目カテゴリー登録に関するお問合せ：<a href='mailto:dem-info@deecorp.jp'>dem-info@deecorp.jp</a></p>";
						strCatTable += "<p>&nbsp;</p>";

						strCatTable += "</div>";
					} else {
						//
						//	全部表示テーブル作成
						//
						strCatTable += "<h2 class='h2_m_top01'>My案件最新情報　あたなの登録済カテゴリーの案件情報をお届けいたします。</h2>";

						//
						//	タブ表示
						//
						if( varDisp6Flg==0 ) {
							//	最新
							strCatTable += "<table border='0' cellspacing='0' cellpadding='0' class='memlist-tab'>";
							strCatTable += "	<tr>";
							strCatTable += "	<td><img src='images/spacer.gif' width='5' height='24' /></td>";
							strCatTable += "	<td><img src='images/members/tab_memlast.gif' width='92' height='24' /></td>";
							strCatTable += "	<td><a class='btn-memtab6month' style='cursor: pointer;' onclick='varDisp6Flg=1; setUserCategory()'>6ヵ月（累計）</a></td>";
							strCatTable += "	<td><img src='images/spacer.gif' width='345' height='24' /></td>";
							strCatTable += "	</tr>";
							strCatTable += "	<tr>";
							strCatTable += "	<td colspan='4'><img src='images/members/tab_memline6month.gif' width='533' height='3' /></td>";
							strCatTable += "	</tr>";
							strCatTable += "</table>";
						} else {
							//	過去
							strCatTable += "<table border='0' cellspacing='0' cellpadding='0' class='memlist-tab'>";
							strCatTable += "	<tr>";
							strCatTable += "    <td><img src='images/spacer.gif' width='5' height='24' /></td>";
							strCatTable += "    <td><a class='btn-memtabLast' style='cursor: pointer;' onclick='varDisp6Flg=0; setUserCategory()'>最新</a></td>";
							strCatTable += "    <td><img src='images/members/tab_mem6month.gif' width='92' height='24' /></td>";
							strCatTable += "    <td><img src='images/spacer.gif' width='345' height='24' /></td>";
							strCatTable += "    <tr>";
							strCatTable += "    <td colspan='4'><img src='images/members/tab_memlinelast.gif' width='533' height='3' /></td>";
							strCatTable += "    </tr>";
							strCatTable += "</table>";
						}
						strCatTable += addMsg+"<table class='tb-cateList'>";
						strCatTable += "<tr><th nowrap='nowrap'>&nbsp;</th><td class='td_amount'>見積依頼総額（円）</td><td class='td_number'>案件件数</td></tr>";
						iiMax=0;
						////for(var ii in objAryUserItemID) {
						for(var kk=0; kk<jjMax; kk++) {
							var ii = numAryDispList[kk];
							if( varDisp6Flg==0 ) {
								varTotal = funkJPNYEN2(objAryTotal[ii]);
								varCount = myFormatNumber(objAryCount[ii]);
							} else {
								varTotal = funkJPNYEN2(objAryTotal6[ii]);
								varCount = myFormatNumber(objAryCount6[ii]);
							}
							if( varTotal == 'undefined' ) varTotal = '0万';
							if( varCount == 'undefined' ) varCount = 0;
							if( varTotal == 'null' ) varTotal = '0万';
							if( varCount == 'null' ) varCount = 0;
							if( varTotal == "" ) varTotal = '0万';
							if( varCount == "" ) varCount = 0;

							var strShoItemName = objAryShoItemName[ii];
							if( varDisp6Flg==0 ) {
								//	最新
								if( varTotal == '0万' && varCount == 0 ) {
									//	金額０　件数０
									strCatTable += "<tr><th nowrap='nowrap'>・"+strShoItemName+"</th><td class='td_amount'>-</td><td class='td_number'>-</td></tr>";
								} else if( varTotal == '0万' ) {
									//	金額がないとき
									if( varCount <= 4 ) {
										//	5件未満
										strCatTable += "<tr><th nowrap='nowrap'>・<a onclick=\"window.open('<%=INI_url%>?item1="+objAryShoItemNo1[ii]+"&item2="+objAryShoItemNo2[ii]+"&item3="+objAryShoItemNo3[ii]+"', 'winName', 'left=0,top=0,width=600,height=600,status=0,scrollbars=1,menubar=0,location=0,toolbar=0,resizable=1');\" href='javascript:;'>"+strShoItemName+"</a></th><td class='td_amount'>現在積算中</td><td class='td_number'>5件未満</td></tr>";
									} else {
										strCatTable += "<tr><th nowrap='nowrap'>・<a onclick=\"window.open('<%=INI_url%>?item1="+objAryShoItemNo1[ii]+"&item2="+objAryShoItemNo2[ii]+"&item3="+objAryShoItemNo3[ii]+"', 'winName', 'left=0,top=0,width=600,height=600,status=0,scrollbars=1,menubar=0,location=0,toolbar=0,resizable=1');\" href='javascript:;'>"+strShoItemName+"</a></th><td class='td_amount'>現在積算中</td><td class='td_number'>"+varCount+"</td></tr>";
									}
								} else {
									if( varCount <= 4 ) {
										//	5件未満
										strCatTable += "<tr><th nowrap='nowrap'>・<a onclick=\"window.open('<%=INI_url%>?item1="+objAryShoItemNo1[ii]+"&item2="+objAryShoItemNo2[ii]+"&item3="+objAryShoItemNo3[ii]+"', 'winName', 'left=0,top=0,width=600,height=600,status=0,scrollbars=1,menubar=0,location=0,toolbar=0,resizable=1');\" href='javascript:;'>"+strShoItemName+"</a></th><td class='td_amount'>"+varTotal+"</td><td class='td_number'>5件未満</td></tr>";
									} else {
										strCatTable += "<tr><th nowrap='nowrap'>・<a onclick=\"window.open('<%=INI_url%>?item1="+objAryShoItemNo1[ii]+"&item2="+objAryShoItemNo2[ii]+"&item3="+objAryShoItemNo3[ii]+"', 'winName', 'left=0,top=0,width=600,height=600,status=0,scrollbars=1,menubar=0,location=0,toolbar=0,resizable=1');\" href='javascript:;'>"+strShoItemName+"</a></th><td class='td_amount'>"+varTotal+"</td><td class='td_number'>"+varCount+"</td></tr>";
									}
								}
							} else {
								//	過去は詳細のリンクなし
								if( varTotal == '0万' && varCount == 0 ) {
									//	金額０　件数０
									strCatTable += "<tr><th nowrap='nowrap'>・"+strShoItemName+"</th><td class='td_amount'>-</td><td class='td_number'>-</td></tr>";
								} else if( varTotal == '0万' ) {
									//	金額がないとき
									if( varCount <= 4 ) {
										//	5件未満
										strCatTable += "<tr><th nowrap='nowrap'>・"+strShoItemName+"</a></th><td class='td_amount'>現在積算中</td><td class='td_number'>5件未満</td></tr>";
									} else {
										strCatTable += "<tr><th nowrap='nowrap'>・"+strShoItemName+"</a></th><td class='td_amount'>現在積算中</td><td class='td_number'>"+varCount+"</td></tr>";
									}
								} else {
									if( varCount <= 4 ) {
										//	5件未満
										strCatTable += "<tr><th nowrap='nowrap'>・"+strShoItemName+"</a></th><td class='td_amount'>"+varTotal+"</td><td class='td_number'>5件未満</td></tr>";
									} else {
										strCatTable += "<tr><th nowrap='nowrap'>・"+strShoItemName+"</a></th><td class='td_amount'>"+varTotal+"</td><td class='td_number'>"+varCount+"</td></tr>";
									}
								}
							}
							iiMax++;
							if( iiMax >= 5 ) break;
						}
						strCatTable += "</table>";

						//	全てみるボタンの表示
						strCatTable += "<p class='notice'>&nbsp;&nbsp;&nbsp;&nbsp;※「-」表示のカテゴリーにつきましては、現在該当案件はございません。</p>";
						if( jjMax > 5 ) {
							strCatTable += "<div class='bBlock-memTop01-btm'><a href='./member_detail_user.jsp?varDisp6Flg="+varDisp6Flg+"' class='btn-memTop-seeall'>全て見る</a></div>";
						}
					}
					obj = document.getElementById("usetItemId");
					obj.innerHTML = strCatTable;
				}

				//**********************************************************
				//	全部カテゴリ表示
				//		input	item_no =	大項目No
				//				flg		=	[0:閉じる,1:開く]
				//**********************************************************
				function setCategory(item_no,flg) {
					//
					//	内部使用変数定義
					//
					var	strCatTable = "";
					var ii;
					var	varTotal=0;
					var	varCount=0;

					//
					//	item_noからflgをセットする
					//
					setFlg(item_no,flg);

					strCatTable += "<h2 class='h2_m_top02'>本日の最新案件情報</h2>";
					strCatTable += "<p>見積＠Deeにおける本日の最新案件情報をお届けしております。</p>";
					strCatTable += "<p>※<font color='#ff6600'>色</font>のついている項目は、あなたの登録カテゴリーです。</p>";

					//
					//	タブ表示
					//
					if( varDisp6Flg2 == 0 ) {
						//	最新
						strCatTable += "<table border='0' cellspacing='0' cellpadding='0' class='catelist-tab'>";
						strCatTable += "<tr>";
						strCatTable += "<td><img src='images/spacer.gif' width='5' height='24' /></td>";
						strCatTable += "<td><img src='images/members/tab_catelast.gif' width='92' height='24' /></td>";
						strCatTable += "<td><a class='btn-catetab6month' style='cursor: pointer;' onclick='varDisp6Flg2=1; setCategory("+item_no+","+flg+")'>6ヵ月（累計）</a></td>";
						strCatTable += "<td><img src='images/spacer.gif' width='325' height='24' /></td>";
						strCatTable += "</tr>";
						strCatTable += "<tr>";
						strCatTable += "<td colspan='4'><img src='images/members/tab_cateline6month.gif' width='513' height='3' /></td>";
						strCatTable += "</tr>";
						strCatTable += "</table>";
					} else {
						//	過去
						strCatTable += "<table border='0' cellspacing='0' cellpadding='0' class='catelist-tab'>";
						strCatTable += "<tr>";
						strCatTable += "<td><img src='images/spacer.gif' width='5' height='24' /></td>";
						strCatTable += "<td><a class='btn-catetabLast' style='cursor: pointer;' onclick='varDisp6Flg2=0; setCategory("+item_no+","+flg+")'>本日</a></td>";
						strCatTable += "<td><img src='images/members/tab_cate6month.gif' width='92' height='24' /></td>";
						strCatTable += "<td><img src='images/spacer.gif' width='325' height='24' /></td>";
						strCatTable += "</tr>";
						strCatTable += "<tr>";
						strCatTable += "<td colspan='4'><img src='images/members/tab_catelinelast.gif' width='513' height='3' /></td>";
						strCatTable += "</tr>";
						strCatTable += "</table>";
					}

					//
					//	全部表示テーブル作成
					//
					strCatTable += "<div class='cateList'>";
					strCatTable += "<table class='tb-cateList'>";
					strCatTable += "<tr><th nowrap='nowrap'><td class='td_amount'>見積依頼総額（円）</td><td class='td_number'>案件件数</td></tr>";
					for( ii=0; ii<iiCategoryMax; ii++ ) {
						//
						//	TOTAL,COUNT
						//
						if( varDisp6Flg2==0 ) {
							varTotal = funkJPNYEN2(objAryTotal[strAryCategoryID[ii]]); // + "&nbsp;DEBUG=" + myFormatNumber(objAryTotal[strAryCategoryID[ii]]);
							varCount = myFormatNumber(objAryCount[strAryCategoryID[ii]]);
						} else {
							varTotal = funkJPNYEN2(objAryTotal6[strAryCategoryID[ii]]); // + "&nbsp;DEBUG=" + myFormatNumber(objAryTotal[strAryCategoryID[ii]]);
							varCount = myFormatNumber(objAryCount6[strAryCategoryID[ii]]);
						}
						if( varTotal == 'undefined' ) varTotal = '0万';
						if( varCount == 'undefined' ) varCount = 0;
						if( varTotal == 'null' ) varTotal = '0万';
						if( varCount == 'null' ) varCount = 0;
						if( varTotal == "" ) varTotal = '0万';
						if( varCount == "" ) varCount = 0;

						//
						//	全部テーブル作成
						//
						var	strCatName = "";
						if( strAryCategoryCOL[ii]==1 ) {
							strCatName = "<font color='#ff6600'>"+strAryCategory[ii]+"</font>";
						} else {
							strCatName = strAryCategory[ii];
						}

						if( strAryCategoryID[ii].length <= 3 ) {
							var strNowItem1 = strAryCategoryID[ii];

							//	大項目表示
							if( strAryCategoryFLG[ii] == 0 && varstrSelItem1 != strNowItem1 ) {
								//	閉じてる状態
								if( varTotal == '0万' && varCount == 0 ) {
									//	金額０　件数０
									strCatTable += "<tr><th nowrap='nowrap'><img src='./images/migi.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',1);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',1);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>-</td><td class='td_number'>-</td></tr>";
								} else if( varTotal == '0万' ) {
									//	金額がないとき
									if( varCount <= 4 ) {
										//	5件未満
										strCatTable += "<tr><th nowrap='nowrap'><img src='./images/migi.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',1);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',1);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>現在積算中</td><td class='td_number'>5件未満</td></tr>";
									} else {
										strCatTable += "<tr><th nowrap='nowrap'><img src='./images/migi.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',1);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',1);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>現在積算中</td><td class='td_number'>"+varCount+"</td></tr>";
									}
								} else {
									if( varCount <= 4 ) {
										//	5件未満
										strCatTable += "<tr><th nowrap='nowrap'><img src='./images/migi.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',1);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',1);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>"+varTotal+"</td><td class='td_number'>5件未満</td></tr>";
									} else {
										strCatTable += "<tr><th nowrap='nowrap'><img src='./images/migi.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',1);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',1);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>"+varTotal+"</td><td class='td_number'>"+varCount+"</td></tr>";
									}
								}
							} else {
								//	開いてる状態
								if( varstrSelItem1 == strNowItem1 ) {
									strAryCategoryFLG[ii] = 1;
									varstrSelItem1="";
								}
								if( varTotal == '0万' && varCount == 0 ) {
									//	金額０　件数０
									strCatTable += "<tr><th nowrap='nowrap'><img src='./images/shita.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',0);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',0);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>-</td><td class='td_number'>-</td></tr>";
								} else if( varTotal == '0万' ) {
									//	金額がないとき
									if( varCount <= 4 ) {
										//	5件未満
										strCatTable += "<tr><th nowrap='nowrap'><img src='./images/shita.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',0);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',0);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>現在積算中</td><td class='td_number'>5件未満</td></tr>";
									} else {
										strCatTable += "<tr><th nowrap='nowrap'><img src='./images/shita.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',0);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',0);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>現在積算中</td><td class='td_number'>"+varCount+"</td></tr>";
									}
								} else {
									if( varCount <= 4 ) {
										//	5件未満
										strCatTable += "<tr><th nowrap='nowrap'><img src='./images/shita.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',0);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',0);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>"+varTotal+"</td><td class='td_number'>5件未満</td></tr>";
									} else {
										strCatTable += "<tr><th nowrap='nowrap'><img src='./images/shita.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',0);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',0);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>"+varTotal+"</td><td class='td_number'>"+varCount+"</td></tr>";
									}
								}
							}
						} else if( strAryCategoryID[ii].length <= 6 ) {
							//	中項目表示
							if( getFlg(strAryCategoryID[ii].substr(0,3)) == 1 ) {
								//	中分類が開いている状態
								if( getFlg(strAryCategoryID[ii].substr(0,6)) == 0 ) {
									//	小分類が閉じている状態
									if( varTotal == '0万' && varCount == 0 ) {
										//	金額０　件数０
										strCatTable += "<tr><th nowrap='nowrap'>&nbsp;&nbsp;<img src='./images/migi.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',1);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',1);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>-</td><td class='td_number'>-</td></tr>";
									} else if( varTotal == '0万' ) {
										//	金額がないとき
										if( varCount <= 4 ) {
											//	5件未満
											strCatTable += "<tr><th nowrap='nowrap'>&nbsp;&nbsp;<img src='./images/migi.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',1);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',1);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>現在積算中</td><td class='td_number'>5件未満</td></tr>";
										} else {
											strCatTable += "<tr><th nowrap='nowrap'>&nbsp;&nbsp;<img src='./images/migi.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',1);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',1);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>現在積算中</td><td class='td_number'>"+varCount+"</td></tr>";
										}
									} else {
										if( varCount <= 4 ) {
											//	5件未満
											strCatTable += "<tr><th nowrap='nowrap'>&nbsp;&nbsp;<img src='./images/migi.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',1);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',1);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>"+varTotal+"</td><td class='td_number'>5件未満</td></tr>";
										} else {
											strCatTable += "<tr><th nowrap='nowrap'>&nbsp;&nbsp;<img src='./images/migi.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',1);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',1);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>"+varTotal+"</td><td class='td_number'>"+varCount+"</td></tr>";
										}
									}
								} else {
									//	小分類が開いている状態
									if( varTotal == '0万' && varCount == 0 ) {
										//	金額０　件数０
										strCatTable += "<tr><th nowrap='nowrap'>&nbsp;&nbsp;<img src='./images/shita.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',0);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',0);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>-</td><td class='td_number'>-</td></tr>";
									} else if( varTotal == '0万' ) {
										//	金額がないとき
										if( varCount <= 4 ) {
											//	5件未満
											strCatTable += "<tr><th nowrap='nowrap'>&nbsp;&nbsp;<img src='./images/shita.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',0);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',0);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>現在積算中</td><td class='td_number'>5件未満</td></tr>";
										} else {
											strCatTable += "<tr><th nowrap='nowrap'>&nbsp;&nbsp;<img src='./images/shita.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',0);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',0);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>現在積算中</td><td class='td_number'>"+varCount+"</td></tr>";
										}
									} else {
										if( varCount <= 4 ) {
											//	5件未満
											strCatTable += "<tr><th nowrap='nowrap'>&nbsp;&nbsp;<img src='./images/shita.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',0);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',0);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>"+varTotal+"</td><td class='td_number'>5件未満</td></tr>";
										} else {
											strCatTable += "<tr><th nowrap='nowrap'>&nbsp;&nbsp;<img src='./images/shita.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',0);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',0);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>"+varTotal+"</td><td class='td_number'>"+varCount+"</td></tr>";
										}
									}
								}
							}
						} else {
							//	小項目表示
							if( getFlg(strAryCategoryID[ii].substr(0,3)) == 1 ) {
								//	中分類が開いている
								if( getFlg(strAryCategoryID[ii].substr(0,6)) == 1 ) {
									//	開いている状態
									if( varCount > 0 ) {
										//	件数あり
										if( varDisp6Flg2==0 ) {
											//	最新
											if( varTotal == '0万' && varCount == 0 ) {
												//	金額０　件数０
												strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;・<a onclick=\"window.open('<%=INI_url%>?item1="+strAryCategoryID[ii].substr(0,3)+"&item2="+strAryCategoryID[ii].substr(0,6)+"&item3="+strAryCategoryID[ii]+"', 'winName', 'left=0,top=0,width=600,height=600,status=0,scrollbars=1,menubar=0,location=0,toolbar=0,resizable=1');\" href='javascript:;'>"+strCatName+"</a></td><td class='td_amount'>-</td><td class='td_number'>-</td></tr>";
											} else if( varTotal == '0万' ) {
												//	金額がないとき
												if( varCount <= 4 ) {
													//	5件未満
													strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;・<a onclick=\"window.open('<%=INI_url%>?item1="+strAryCategoryID[ii].substr(0,3)+"&item2="+strAryCategoryID[ii].substr(0,6)+"&item3="+strAryCategoryID[ii]+"', 'winName', 'left=0,top=0,width=600,height=600,status=0,scrollbars=1,menubar=0,location=0,toolbar=0,resizable=1');\" href='javascript:;'>"+strCatName+"</a></td><td class='td_amount'>現在積算中</td><td class='td_number'>5件未満</td></tr>";
												} else {
													strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;・<a onclick=\"window.open('<%=INI_url%>?item1="+strAryCategoryID[ii].substr(0,3)+"&item2="+strAryCategoryID[ii].substr(0,6)+"&item3="+strAryCategoryID[ii]+"', 'winName', 'left=0,top=0,width=600,height=600,status=0,scrollbars=1,menubar=0,location=0,toolbar=0,resizable=1');\" href='javascript:;'>"+strCatName+"</a></td><td class='td_amount'>現在積算中</td><td class='td_number'>"+varCount+"</td></tr>";
												}
											} else {
												if( varCount <= 4 ) {
													//	5件未満
													strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;・<a onclick=\"window.open('<%=INI_url%>?item1="+strAryCategoryID[ii].substr(0,3)+"&item2="+strAryCategoryID[ii].substr(0,6)+"&item3="+strAryCategoryID[ii]+"', 'winName', 'left=0,top=0,width=600,height=600,status=0,scrollbars=1,menubar=0,location=0,toolbar=0,resizable=1');\" href='javascript:;'>"+strCatName+"</a></td><td class='td_amount'>"+varTotal+"</td><td class='td_number'>5件未満</td></tr>";
												} else {
													strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;・<a onclick=\"window.open('<%=INI_url%>?item1="+strAryCategoryID[ii].substr(0,3)+"&item2="+strAryCategoryID[ii].substr(0,6)+"&item3="+strAryCategoryID[ii]+"', 'winName', 'left=0,top=0,width=600,height=600,status=0,scrollbars=1,menubar=0,location=0,toolbar=0,resizable=1');\" href='javascript:;'>"+strCatName+"</a></td><td class='td_amount'>"+varTotal+"</td><td class='td_number'>"+varCount+"</td></tr>";
												}
											}
										} else {
											//	過去は詳細のリンクなし
											if( varTotal == '0万' && varCount == 0 ) {
												//	金額０　件数０
												strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;・"+strCatName+"</a></td><td class='td_amount'>-</td><td class='td_number'>-</td></tr>";
											} else if( varTotal == '0万' ) {
												//	金額がないとき
												if( varCount <= 4 ) {
													//	5件未満
													strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;・"+strCatName+"</a></td><td class='td_amount'>現在積算中</td><td class='td_number'>5件未満</td></tr>";
												} else {
													strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;・"+strCatName+"</a></td><td class='td_amount'>現在積算中</td><td class='td_number'>"+varCount+"</td></tr>";
												}
											} else {
												if( varCount <= 4 ) {
													//	5件未満
													strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;・"+strCatName+"</a></td><td class='td_amount'>"+varTotal+"</td><td class='td_number'>5件未満</td></tr>";
												} else {
													strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;・"+strCatName+"</a></td><td class='td_amount'>"+varTotal+"</td><td class='td_number'>"+varCount+"</td></tr>";
												}
											}
										}
									} else {
										//	件数なし
										if( varTotal == '0万' && varCount == 0 ) {
											//	金額０　件数０
											strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;・"+strCatName+"</a></td><td class='td_amount'>-</td><td class='td_number'>-</td></tr>";
										} else if( varTotal == '0万' ) {
											//	金額がないとき
											if( varCount <= 4 ) {
												//	5件未満
												strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;・"+strCatName+"</a></td><td class='td_amount'>現在積算中</td><td class='td_number'>5件未満</td></tr>";
											} else {
												strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;・"+strCatName+"</a></td><td class='td_amount'>現在積算中</td><td class='td_number'>"+varCount+"</td></tr>";
											}
										} else {
											if( varCount <= 4 ) {
												//	5件未満
												strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;・"+strCatName+"</a></td><td class='td_amount'>"+varTotal+"</td><td class='td_number'>5件未満</td></tr>";
											} else {
												strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;・"+strCatName+"</a></td><td class='td_amount'>"+varTotal+"</td><td class='td_number'>"+varCount+"</td></tr>";
											}
										}
									}
								}
							}
						}
					}
					strCatTable += "</table>";
					strCatTable += "<p class='notice'>&nbsp;&nbsp;&nbsp;&nbsp;※「-」表示のカテゴリーにつきましては、現在該当案件はございません。</p>";
					strCatTable += "</div>";
					obj = document.getElementById("catListAll");
					obj.innerHTML = strCatTable;
				}
		//-->
		</script>
	</head>

<!-- 最新情報を処理をしない(09/01/08)
	<body onload="setUserCategory(); setCategory();" oncontextmenu="return false"><a name="pagetop" id="pagetop"></a>
-->
	<body oncontextmenu="return false"><a name="pagetop" id="pagetop"></a>
		<div id="wrapper" class="biz2nd">
			<!-- header -->
			<div id="header">
				<p id="logo-biz"><a href="http://bb.deecorp.jp"><img src="../images/biz/biz_logo.gif" alt="DeeCorp" width="140" height="28" border="0"></a></p>
				<ul id="hd-submenu">
					<li><a href="https://www2.deecorp.jp/dee-hp/contact_input.jsp"><img src="../images/btn_contact.gif" alt="お問い合わせ" width="62" height="10" border="0" /></a></li>
				</ul>
			</div>
			<!-- globalMenu -->
			<ul id="globalMenu-sh">
			<li class="g1"><a href="http://www.deecorp.jp">DeeCorp HP</a></li>
			<li class="g2"><a href="http://bb.deecorp.jp">Biz@Dee</a></li>
			<li class="g3"></li>
			<li class="g4"></li>
			<li class="g5"></li>
			<li class="g6"></li>
			</ul>
			<!-- contentBody -->
			<div id="contentBody">
				<!-- column-main -->
				<div id="clm-main">
					<div style="padding-bottom:5px;"><img src="images/members/keyVisual.jpg" alt="Business@DeeBank会員専用ページ" width="535" height="170" /></div>
					<p class="loginMessage">いつも＠Deeサービスをご利用いただき、ありがとうございます。</p>
	                <table class="loginStatus">
	                	<tr>
	                    <td width="1%"><img src="images/members/icon_mem.gif" alt="*" width="19" height="19" /></td>
						<td><%=user_name%>　様&nbsp;&nbsp;&nbsp;&nbsp;<%=strLoginDate%> 現在</td>
	                    <td width="10%"><a href="./member_logout.jsp"><img src="images/members/btn_mem_logout.gif" alt="ログアウト" width="82" height="23" border="0" /></a></td>
						</tr>
	                </table>

<!-- 見積@Deeリニューアルにともなう変更箇所（その２）ここから（09/01/07） -->
                
<img src="images/members/renewal/img_renewalTop.jpg" alt="見積@Deeリニューアル！" border="0" usemap="#img_renewalTopMap" id="img_renewalTop" />
<map name="img_renewalTopMap" id="img_renewalTopMap">
  <area shape="rect" coords="185,217,350,275" href="http://www2.deecorp.jp/dee-tutorial/" target="_blank" alt="見積@Dee体験版" />
  <area shape="rect" coords="362,217,525,275" href="./renewal.html" alt="見積@Deeリニューアル概要" />
<area shape="rect" coords="8,217,174,275" href="https://with.deecorp.jp/dee/dem2/" target="_blank" />
</map>

<!-- 見積@Deeリニューアルにともなう変更箇所（その２）ここまで（09/01/07） -->
	                
<!-- 最新情報を処理をしない(09/01/08)
	                <div id="usetItemId" name="usetItemId" class="bBlock-memTop01"></div>
					<div id="catListAll" name="catListAll" class="bBlock-memTop02"></div>
-->
	                <DIV class=backto-top><A href="#pagetop"><IMG alt=ページトップに戻る src="../images/btn_backtotop.gif"></A></DIV>
			  	</div>
				<!-- column-left -->
				<div id="clm-left">

<!-- 修正箇所ここから：左メニューの画像化にともなうタグ修正（リンク先は現状の情報を使用してください） -->

<!-- 見積@Deeリニューアルにともなう変更箇所（その１）ここから（09/01/07） -->
            
				<div id="regionalMenu">
					<p><img src="images/members/rmenu_mem.gif" alt="各種サービスログイン" width="165" height="50" /></p>
					<ul id="rgmenu_mem">
						<li class="rg1"><a href="https://with.deecorp.jp/dee/dem2/" target="_blank">見積@Dee</a></li>
						<li class="rg3"><a href="https://with.deecorp.jp/dee/dsp/pcapp/" target="_blank">案件インデックス機能</a></li>
						<li class="rg4"><a href="http://www2.deecorp.jp/dee-tutorial/" target="_blank">見積@Deeリニューアル体験版</a></li>
                        <li class="rg5"><a href="./renewal.html">見積@Deeリニューアル概要</a></li>
                        <li class="rg8"><a href="https://with.deecorp.jp/dee/ptl2/Query.action" target="_blank">よくある質問</a></li>
					</ul>
				</div>
                
                <p>※<a href="http://bb.deecorp.jp/important/keiyaku081217.html" target="_blank">契約@Dee Free版</a><br />　<a href="http://bb.deecorp.jp/important/keiyaku081217.html" target="_blank">サービス終了について</a></p>
                
<!-- 見積@Deeリニューアルにともなう変更箇所（その１）ここまで（09/01/07） -->
                
<!-- 修正箇所ここまで：左メニューの画像化にともなうタグ修正 -->
			  </div>
			</div>
			<!-- footer -->
			<p id="utility-footer"><a href="http://www.deecorp.jp/utility/security.html" target="_blank">セキュリティ基本方針</a>　｜　<a href="http://www.deecorp.jp/utility/privacy2.html" target="_blank">個人情報保護方針</a>　｜　<a href="http://www.deecorp.jp/utility/privacy.html" target="_blank">個人情報の取り扱い</a></p>
			<p id="footer-corp">ディーコープ株式会社 / 私たちはソフトバンクグループです</p>
			<p id="copyright">Copyright &copy; 2008 DeeCorp. All rights reserved.</p>
		</div>

		<script language="JavaScript">
		<!--//
			//
			//	内部カテゴリテーブル作成
			//
			<%
/* 最新情報を処理をしない(09/01/08)
			int iNowCategoryMax=0;
			int jj=0;
			for( int i=0; i<iCategoryMax; i++ ) {
				if( !strAryCategoryID[i].equals("800") ) {		//	流通仕入は表示なし
					if( !strAryCategoryID[i].equals("900") ) {	//	ショップ管理用は表示なし
						out.print("strAryCategory["+jj+"]='"+strAryCategory[i]+"'; ");
						out.print("strAryCategoryID["+jj+"]='"+strAryCategoryID[i]+"'; ");
						out.print("strAryCategoryFLG["+jj+"]=0; ");
						out.print("strAryCategoryCOL["+jj+"]=0; ");
						iNowCategoryMax++;
						jj++;
					}
				}
			}
			out.print("iiCategoryMax = "+iNowCategoryMax+";");

			//	TOTAL
			Object[] objKey1=objAryTotal.keySet().toArray();
			for(int i=0;i<objKey1.length;i++){
				if( objAryTotal.get(objKey1[i]) == null ) {
					out.print("objAryTotal['"+objKey1[i]+"']='0'; ");
				} else {
					out.print("objAryTotal['"+objKey1[i]+"']='"+objAryTotal.get(objKey1[i])+"'; ");
				}
			}

			//	TOTAL(6ヶ月)
			Object[] objKey16=objAryTotal6.keySet().toArray();
			for(int i=0;i<objKey16.length;i++){
				if( objAryTotal6.get(objKey16[i]) == null ) {
					out.print("objAryTotal6['"+objKey16[i]+"']='0'; ");
				} else {
					out.print("objAryTotal6['"+objKey16[i]+"']='"+objAryTotal6.get(objKey16[i])+"'; ");
				}
			}

			//	COUNT
			Object[] objKey2=objAryCount.keySet().toArray();
			for(int i=0;i<objKey2.length;i++){
				out.print("objAryCount['"+objKey2[i]+"']='"+objAryCount.get(objKey2[i])+"'; ");
			}

			//	COUNT(6ヶ月)
			Object[] objKey26=objAryCount6.keySet().toArray();
			for(int i=0;i<objKey26.length;i++){
				out.print("objAryCount6['"+objKey26[i]+"']='"+objAryCount6.get(objKey26[i])+"'; ");
			}

			//	USER ITEM
			Object[] objKey3=objAryUserItemID.keySet().toArray();
			for(int i=0;i<objKey3.length;i++){
				String strDumobjKey3 = (String)objKey3[i];
				if( strDumobjKey3.substring(0,3).equals("800") ) {
					//	流通仕入れは表示しない
				} else if( strDumobjKey3.substring(0,3).equals("900") ) {
					//	ショップ管理用は表示しない
				} else {
					out.print("objAryUserItemID['"+objKey3[i]+"']='"+objAryUserItemID.get(objKey3[i])+"'; ");
				}
			}

			//	SHO ITEM
			Object[] objKey4=objAryShoItemName.keySet().toArray();
			String strItem;
			for(int i=0;i<objKey4.length;i++){
				out.print("objAryShoItemName['"+objKey4[i]+"']='"+objAryShoItemName.get(objKey4[i])+"'; ");
				strItem=(String)objKey4[i];
				if( strItem.length() >= 3 ) {
					out.print("objAryShoItemNo1['"+objKey4[i]+"']='"+strItem.substring(0,3)+"'; ");
				} else {
					out.print("objAryShoItemNo1['"+objKey4[i]+"']='999'; ");
				}
				if( strItem.length() >= 6 ) {
					out.print("objAryShoItemNo2['"+objKey4[i]+"']='"+strItem.substring(0,6)+"'; ");
				} else {
					out.print("objAryShoItemNo2['"+objKey4[i]+"']='999999'; ");
				}
				out.print("objAryShoItemNo3['"+objKey4[i]+"']='"+strItem+"'; ");
			}
*/
			%>
		//-->
		</script>
	</body>
	</html>
<%
	//**********************************************************************************************
	//	DB切断
	//**********************************************************************************************
	//	データベースから切断
	stmt.close();
	//	接続クローズ
	db.close();
	//	データベースから切断
	stmt2.close();
	//	接続クローズ
	db2.close();
%>
