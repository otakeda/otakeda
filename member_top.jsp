<%@ page import="java.net.*,java.io.*,java.util.*,java.sql.*;" contentType="text/html; charset=Shift_JIS"%>
<%@ page buffer="none" autoFlush="true" %>
<%
//**********************************************************************************************
//	BB@Dee �T�v���C����������T�C�g
//		MAKE DATE	2007/04/20	Ver1.00	by	HS
//		EDIT DATE	2007/04/20	Ver1.00	by	HS	���e
//		EDIT DATE	2007/05/11	Ver1.01	by	HS	�����ύX
//		EDIT DATE	2007/05/15	Ver1.02	by	HS	�����ύX
//		EDIT DATE	2007/05/24	Ver1.03	by	HS	�ߋ��U�P���\��
//		EDIT DATE	2007/07/13	Ver1.04	by	HS	���ʎd���\���Ȃ��̂Ƃ���ɃV���b�v�Ǘ��p���ǉ�
//		EDIT DATE	2008/05/10	Ver2.00	by	HS	���j���[�A��
//		EDIT DATE	2008/06/11	Ver2.01	by	HS	�����ޑΉ�
//		EDIT DATE	2008/06/21	Ver2.02	by	HS	�f�U�C���C��
//		EDIT DATE	2008/06/26	Ver2.03 by	HS	1���̎��ɋ��z���O�ɂ���̂���߂�
//		EDIT DATE	2008/06/30	Ver2.04 by	HS	���ρ��c�����̂k�h�m�j�ύX
//		EDIT DATE	2008/12/16	Ver2.05 by	HS	���j���[�A�������N�̒ǉ��E�����j���[�̉��C
//		EDIT DATE	2009/01/08	Ver2.06 by	HS	�����j���[�̉��C�E�ŐV�����\��
//**********************************************************************************************

//**********************************************************************************************
//	INI�t�@�C���ǂݍ���
//**********************************************************************************************
	//
	//	�����g�p�ϐ���`
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

	String	INI_url				="";	//	�ڍ׉�ʂւ̃����N

	//
	//	�ǂݍ���
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
//	JDBC�h���C�o���[�h
//**********************************************************************************************
    Class.forName(INI_userdb_drv);
    Class.forName(INI_itemdb_drv);

//**********************************************************************************************
//	DB�ڑ�
//**********************************************************************************************
	//	UserDB�ڑ�
	String		Userdsn = "jdbc:"+INI_userdb_jdbc+"://"+INI_userdb_ip+":"+INI_userdb_port+"/"+INI_userdb_db+"?user="+INI_userdb_id+"&password="+INI_userdb_passwd;
	Connection	db = DriverManager.getConnection(Userdsn);
	Statement	stmt = db.createStatement();
	String		sql = "";
	ResultSet	rs = null;

	//	ItemDB�ڑ�
	String		Itemdsn = "jdbc:"+INI_itemdb_jdbc+"://"+INI_itemdb_ip+":"+INI_itemdb_port+"/"+INI_itemdb_db+"?user="+INI_itemdb_id+"&password="+INI_itemdb_passwd;
	Connection	db2 = DriverManager.getConnection(Itemdsn);
	Statement	stmt2 = db2.createStatement();
	String		sql2 = "";
	ResultSet	rs2 = null;

//**********************************************************************************************
//	member_detail.jsp����̈����̎擾
//		i1				=	�S�J�e�S���̕\���ʒu
//		varDisp6Flg		=	����o�^�̃^�u�ʒu
//		varDisp6Flg2	=	�S�J�e�S���̃^�u�ʒu
//**********************************************************************************************
	String strSelItem1		= request.getParameter("i1");
	if( strSelItem1 == null ) strSelItem1 = "";

	String varDisp6Flg		= request.getParameter("varDisp6Flg");
	String varDisp6Flg2		= request.getParameter("varDisp6Flg2");
	if( varDisp6Flg == null ) varDisp6Flg = "0";		//	�����͍ŐV��\��
	if( varDisp6Flg2 == null ) varDisp6Flg2 = "0";		//	�����͍ŐV��\��

//**********************************************************************************************
//	���O�C���ς݊m�F
//**********************************************************************************************
	// �Z�b�V�����ϐ��̎擾
	String strLoginid = (String)session.getAttribute("edu.yale.its.tp.cas.client.filter.user");
	String strLoginDate;

	//
	//	���t�̎擾
	//
	java.util.Date logindate = new java.util.Date();
	int iyy = logindate.getYear();            // 0 = 1900 �N
	int imm = logindate.getMonth();           // 0 = 1 ��
	int idd = logindate.getDate();
	int ihh = logindate.getHours();
	int imn = logindate.getMinutes();
	String strhh = Integer.toString(ihh);
	if( strhh.length() < 2 ) strhh = "0" + strhh;
	String strmn = Integer.toString(imn);
	if( strmn.length() < 2 ) strmn = "0" + strmn;
	strLoginDate = (iyy+1900) + "�N" + (imm+1) + "��" + idd + "��&nbsp;" + strhh + ":" + strmn;

//**********************************************************************************************
//	�u���E�U�̃L���b�V���𖳌��ɂ���B
//		Last-Modified(�ŏI�X�V��) : �{��
//		Expires(�L������)         : �ߋ���(1970/01/01)
//		pragma no-cache           : HTTP1.0�d�l�Ɋ�Â��u�L���b�V�������w���v
//		Cache-Control no-cache    : HTTP1.1�d�l�Ɋ�Â��u�L���b�V�������w���v
//**********************************************************************************************
	java.util.Calendar objCal1=java.util.Calendar.getInstance();
	java.util.Calendar objCal2=java.util.Calendar.getInstance();
	objCal2.set(2000,0,1,0,0,0);
	response.setDateHeader("Expires",objCal2.getTime().getTime());
	response.setHeader("progma","no-cache");
	response.setHeader("Cache-Control","no-cache");

//**********************************************************************************************
//	����`�F�b�N
//**********************************************************************************************
	//	���O�C���t���O
	int		iLogin_flg			=	0;	//	���O�C���t���O[0:none,1:OK]
	String	regist_login_id 	=	"";
	String	regist_password 	=	"";
	String	user_name			=	"";
	String	strItemID			=	"";
	String	delete_flag 		=	"";
	String	dum_regist_login_id =	"";
	String	dum_regist_password =	"";
	String	dum_user_name		=	"";


	//	�o�^�J�e�S��
	HashMap objAryUserItemID = new HashMap();

	//==========================================================================================
	//	�ʏ����̃`�F�b�N
	//==========================================================================================
	if( iLogin_flg != 1 ) {
		//	���R�[�h�Z�b�g�ڑ�
		sql = "SELECT v_supplier.login_id,v_supplier.password,v_supplier.person_name,v_sup_request_item.delete_flag,v_sup_request_item.item_id FROM v_supplier LEFT OUTER JOIN v_sup_request_item ON v_supplier.sup_user_id = v_sup_request_item.sup_id where v_supplier.login_id = '"+strLoginid+"'";
		rs = stmt.executeQuery(sql);

		//	�ǂݍ���
		while(rs.next()){
			dum_regist_login_id = rs.getString("login_id");		//	LOGIN ID���擾
			dum_regist_password = rs.getString("password");		//	PASSWORD���擾
			dum_user_name	   = rs.getString("person_name");		//	�S���Җ����擾

			if( dum_regist_login_id.equals(strLoginid) ) {
				//	LOGIN OK
				iLogin_flg = 1;

				regist_login_id	=	dum_regist_login_id;	//	LOGIN ID���擾
				regist_password =	dum_regist_password;	//	PASSWORD���擾
				user_name		=	dum_user_name;			//	�S���Җ����擾

				delete_flag 	= rs.getString("delete_flag");			//	�폜�t���O���擾
				if( delete_flag == null || delete_flag.equals("0") ) {
					strItemID		= rs.getString("item_id");				//	�o�^�J�e�S��
					if( strItemID != null ) {
						if( strItemID.length() == 9 ) {
							objAryUserItemID.put(strItemID,"1");			//	�����ނ�������Ȃ�
						}
					}
				}
			}
		}

		//	���R�[�h�Z�b�g�ؒf
		rs.close();
	}

//**********************************************************************************************
//	HTML�\��
//**********************************************************************************************
	//**********************************************************************************************
	//	�S���ގ擾
	//**********************************************************************************************
	String[] strAryCategory		=	new String[2000];	//	�J�e�S������
	String[] strAryCategoryID	=	new String[2000];	//	�J�e�S��ID
	HashMap objAryShoItemName	=	new HashMap();		//	�����ޖ�
	int	iCategoryMax			= 0;

	//	���R�[�h�Z�b�g�ڑ�
	sql = "SELECT item_id,item_name FROM v_item order by item_id";
	rs = stmt.executeQuery(sql);

	//	�ǂݍ���
	iCategoryMax=0;
	while(rs.next()){
		strAryCategoryID[iCategoryMax]	= rs.getString("item_id");		// ITEMID���擾
		if( strAryCategoryID[iCategoryMax].length() <= 6 ) {
			//	�啪�ށE�����ނ̂ݒ��o
			strAryCategory[iCategoryMax]	= rs.getString("item_name");	// ITEM���̂��擾
			iCategoryMax++;
			if( iCategoryMax > 2000 ) break;
		} else {
			//	�����ޒ��o
			objAryShoItemName.put(rs.getString("item_id"),rs.getString("item_name"));			//	�����ނ�������Ȃ�

			//	�����ނ����o
			strAryCategory[iCategoryMax]	= rs.getString("item_name");	// ITEM���̂��擾
			iCategoryMax++;
			if( iCategoryMax > 2000 ) break;
		}
	}

	//**********************************************************************************************
	//	�S���ޏW�v
	//		�W�v���x���グ�邽��HASH�g�p�ɕύX�Bby SSK
	//**********************************************************************************************
	//	�ŐV���
	HashMap objAryTotal=new HashMap();
	HashMap objAryCount=new HashMap();
	Hashtable objAryRemainder=new Hashtable();	//	�咆���ޗp�̂P���f�[�^���z
	//	�ߋ��U����
	HashMap objAryTotal6=new HashMap();
	HashMap objAryCount6=new HashMap();
	Hashtable objAryRemainder6=new Hashtable();	//	�咆���ޗp�̂P���f�[�^���z

	//==========================================================================================
	//	���o����
	//==========================================================================================
	//
	//	���ݓ����擾
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
	//	WHERE����
	//
	String	strWhere = "";
	String	strWhere6 = "";
//	strWhere  += " WHERE prop_entry_limit_date>now() and request_state<>3 and market_id<>10 and (buy_admin_state=1 or buy_admin_state=4 or buy_admin_state=5 or buy_admin_state=11 or buy_admin_state=84 or buy_admin_state=85) ";
//	strWhere6 += " WHERE prop_entry_limit_date>=(timestamp '"+bfdate.toLocaleString()+"') and prop_entry_limit_date<=(timestamp '"+nwdate.toLocaleString()+"') and market_id<>10 and (buy_admin_state=6 or buy_admin_state=7 or buy_admin_state=10 or buy_admin_state=13 or buy_admin_state=91) ";

	strWhere  += " WHERE prop_entry_limit_date>now() and request_state<>3 and (buy_admin_state=1 or buy_admin_state=4 or buy_admin_state=5 or buy_admin_state=11 or buy_admin_state=84 or buy_admin_state=85) ";
	strWhere6 += " WHERE prop_entry_limit_date>=(timestamp '"+bfdate.toLocaleString()+"') and request_state<>1 and prop_entry_limit_date<=(timestamp '"+nwdate.toLocaleString()+"') and (buy_admin_state<>7 and buy_admin_state<>8 and  buy_admin_state<>13) ";

	//==========================================================================================
	//	�����ނ̏W�v
	//==========================================================================================
	sql2 = "SELECT item3,sum(sbb_price) AS total,count(item3) AS CNT FROM v_tab_request "+strWhere+" GROUP BY item3 ORDER BY item3";
	rs2 = stmt2.executeQuery(sql2);
	while(rs2.next()){
		String strItem3 = rs2.getString("item3");
		String strTotl3 = rs2.getString("total");
		String strCont3 = rs2.getString("CNT");
		objAryCount.put(strItem3,strCont3);	// ����

		//	�����̐��l��
		int	iCont3 = 0;
		try {
			iCont3 = Integer.parseInt(strCont3);
		} catch(Exception e) {
		}

		//	���z�̐��l��
		int	iTotl3 = 0;
		try {
			iTotl3 = Integer.parseInt(strTotl3);
		} catch(Exception e) {
		}

/*	1���̎��ɋ��z���O�ɂ���̂���߂�
		if( iCont3 == 1 ) {
			//	�����ނ̏W�v
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

			//	�啪�ނ̏W�v
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

			objAryTotal.put(strItem3,"0");	// ���z
		} else {
*/
			objAryTotal.put(strItem3,strTotl3);	// ���z
/*
		}
*/
	}
	rs2.close();

	//==========================================================================================
	//	�����ނ̏W�v
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

		objAryTotal.put(strItem2,strRemainderMid);		// ���z
		objAryCount.put(strItem2,rs2.getString("CNT"));	// ����
	}
	rs2.close();

	//==========================================================================================
	//	�啪�ނ̏W�v
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

		objAryTotal.put(strItem1,strRemainderBig);		// ���z
		objAryCount.put(strItem1,rs2.getString("CNT"));	// ����
	}
	rs2.close();

	//==========================================================================================
	//	�ߋ��U���������ނ̏W�v
	//==========================================================================================
	sql2 = "SELECT item3,sum(sbb_price) AS total,count(item3) AS CNT FROM v_tab_request "+strWhere6+" GROUP BY item3 ORDER BY item3";
	rs2 = stmt2.executeQuery(sql2);

	while(rs2.next()){
		String strItem3 = rs2.getString("item3");
		String strTotl3 = rs2.getString("total");
		String strCont3 = rs2.getString("CNT");
		objAryCount6.put(strItem3,strCont3);	// ����
		objAryTotal6.put(strItem3,strTotl3);	// ���z
	}
	rs2.close();

	//==========================================================================================
	//	�ߋ��U���������ނ̏W�v
	//==========================================================================================
	sql2 = "SELECT item2,sum(sbb_price) AS total,count(item2) AS CNT FROM v_tab_request "+strWhere6+" GROUP BY item2 ORDER BY item2";
	rs2 = stmt2.executeQuery(sql2);
	while(rs2.next()){
		String strItem2 = rs2.getString("item2");
		objAryTotal6.put(strItem2,rs2.getString("total"));	// ���z
		objAryCount6.put(strItem2,rs2.getString("CNT"));	// ����
	}
	rs2.close();

	//==========================================================================================
	//	�ߋ��U�����啪�ނ̏W�v
	//==========================================================================================
	sql2 = "SELECT item1,sum(sbb_price) AS total,count(item1) AS CNT FROM v_tab_request "+strWhere6+" GROUP BY item1 ORDER BY item1";
	rs2 = stmt2.executeQuery(sql2);
	while(rs2.next()){
		String strItem1 = rs2.getString("item1");
		String strTotl1 = rs2.getString("total");
		objAryTotal6.put(strItem1,rs2.getString("total"));	// ���z
		objAryCount6.put(strItem1,rs2.getString("CNT"));	// ����
	}
	rs2.close();

//**********************************************************************************************
//	���O�C��������
//**********************************************************************************************
%>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS" />
		<title>Biz@Dee  --DeeCorp--</title>
		<meta name="keywords" content="�\�t�g�o���N�O���[�v,DeeCorp,����@Dee,���o�[�X�I�[�N�V����,�R�X�g�팸,����,���B,�o��팸,�}�l�[�W�����g,��������,�w��,SOX�@,�Ɩ�����,�o��팸����,�w���Ǘ�,�Ǘ��w��,�w���S��,�R�X�g�팸����,�Ɩ����P,�w���Ɩ�,�O���[�����B,�w���ӗ~,�l�b�g�w��,�w���Ǘ��K��,�w���Ǘ��K��,�Г��R�X�g�팸,�d�q���D,�K�����i,�o�C���[,�T�v���C���[,�T�v���C��">
		<meta name="description" content="�f�B�[�R�[�v�������">
		<link href="../css/common.css" rel="stylesheet" type="text/css" media="screen,print" />
		<script language="JavaScript" src="js/common.js" type="text/javascript"></script>
		<script language="JavaScript">
		<!--//
				//**********************************************************
				//	�O���[�o���ϐ���`
				//**********************************************************
				var strAryCategory		= new Array();
				var strAryCategoryCOL	= new Array();
				var	strAryCategoryID	= new Array();
				var	strAryCategoryFLG	= new Array();
				var objAryTotal 		= new Object();  		// Object�̐���
				var objAryCount 		= new Object();  		// Object�̐���
				var objAryTotal6 		= new Object();  		// Object�̐���
				var objAryCount6 		= new Object();  		// Object�̐���
				var objAryUserItemID	= new Object();  		// Object�̐���
				var	objAryShoItemName	= new Object();  		// Object�̐���
				var	iiCategoryMax;
				var	varstrSelItem1 		= "<%=strSelItem1%>";	//	member_detail����
				var	varDisp6Flg			= <%=varDisp6Flg%>;		//	���[�U�[	�ŐV�E�U�P���f�[�^�ؑփt���O[0:�ŐV,1:�ߋ�]
				var	varDisp6Flg2		= <%=varDisp6Flg2%>;	//	�S�J�e�S��	�ŐV�E�U�P���f�[�^�ؑփt���O[0:�ŐV,1:�ߋ�]
				var	objAryShoItemNo1	= new Object();  	// Object�̐���
				var	objAryShoItemNo2	= new Object();  	// Object�̐���
				var	objAryShoItemNo3	= new Object();  	// Object�̐���

				//**********************************************************
				//	�����\�L
				//**********************************************************
				function funkJPNYEN2(mony){
					if( Number(mony) == 0 ) return "0��";
					if( Number(mony) < 10000 ) return funkJPNYEN(mony);
					var ii	=	0;
				    var s = "" + mony; // �m���ɕ�����^�ɕϊ�����.
				    var p = s.indexOf("."); // �����_�̈ʒu��0�I���W���ŋ��߂�B
				    if (p < 0) { // �����_��������Ȃ�������
				        p = s.length; // ���z�I�ȏ����_�̈ʒu�Ƃ���
				    }
				    var r = s.substring(p, s.length); // �����_�̌��Ə����_���E���̕�����B
				    for (var i = 0; i < p; i++) { // (10 ^ i) �̈ʂɂ���
				        var c = s.substring(p - 1 - i, p - 1 - i + 1); // (10 ^ i) �̈ʂ̂ЂƂ̌��̐���
				        if (c < "0" || c > "9") { // �����ȊO�̂���(�����Ȃ�)����������
				            r = s.substring(0, p - i) + r; // �c���S���t������
				            break;
				        }
				        if(i == 4){
							r = "��"; //+ r;

						}
				        if(i == 7){
							r = ","+ r;

						}
				        if(i == 8){
							r = "��" + r; 
						}
				         if (i > 8 && (i-8) % 3 == 0) { // 3 �����ƁA����������͏���
				            r = "," + r; // �J���}��t������
				        }
				       r = c + r; // �������ꌅ�ǉ�����B
				 
				   }
				//	alert(r);
				    return r;
				}
				function funkJPNYEN(mony){
					var ii	=	0;
				    var s = "" + mony; // �m���ɕ�����^�ɕϊ�����B��ł� "95839285734.3245"
				    var p = s.indexOf("."); // �����_�̈ʒu��0�I���W���ŋ��߂�B��ł� 11
				    if (p < 0) { // �����_��������Ȃ�������
				        p = s.length; // ���z�I�ȏ����_�̈ʒu�Ƃ���
				    }
				    var r = s.substring(p, s.length); // �����_�̌��Ə����_���E���̕�����B��ł� ".3245"
				    for (var i = 0; i < p; i++) { // (10 ^ i) �̈ʂɂ���
				        var c = s.substring(p - 1 - i, p - 1 - i + 1); // (10 ^ i) �̈ʂ̂ЂƂ̌��̐����B��ł� "4", "3", "7", "5", "8", "2", "9", "3", "8", "5", "9" �̏��ɂȂ�B
				        if (c < "0" || c > "9") { // �����ȊO�̂���(�����Ȃ�)����������
				            r = s.substring(0, p - i) + r; // �c���S���t������
				            break;
				        }
				        if(i == 4){
							r = "��";//+ r;

						}
				        if(i == 8){
							r = "��" + r; 
						}
				        r = c + r; // �������ꌅ�ǉ�����B
				    }
					////alert(r);
				    return r; // ��ł� "95,839,285,734.3245"
				}

				//**********************************************************
				// (���ׂĂ̕ϐ��Ɋi�[����l��0�I���W���Ƃ���) 
				//**********************************************************
				function myFormatNumber(x) { // �����̗�Ƃ��Ă� 95839285734.3245
				    var s = "" + x; // �m���ɕ�����^�ɕϊ�����B��ł� "95839285734.3245"
				    var p = s.indexOf("."); // �����_�̈ʒu��0�I���W���ŋ��߂�B��ł� 11
				    if (p < 0) { // �����_��������Ȃ�������
				        p = s.length; // ���z�I�ȏ����_�̈ʒu�Ƃ���
				    }
				    var r = s.substring(p, s.length); // �����_�̌��Ə����_���E���̕�����B��ł� ".3245"
				    for (var i = 0; i < p; i++) { // (10 ^ i) �̈ʂɂ���
				        var c = s.substring(p - 1 - i, p - 1 - i + 1); // (10 ^ i) �̈ʂ̂ЂƂ̌��̐����B��ł� "4", "3", "7", "5", "8", "2", "9", "3", "8", "5", "9" �̏��ɂȂ�B
				        if (c < "0" || c > "9") { // �����ȊO�̂���(�����Ȃ�)����������
				            r = s.substring(0, p - i) + r; // �c���S���t������
				            break;
				        }
				        if (i > 0 && i % 3 == 0) { // 3 �����ƁA����������͏���
				            r = "," + r; // �J���}��t������
				        }
				        r = c + r; // �������ꌅ�ǉ�����B
				    }
				    return r; // ��ł� "95,839,285,734.3245"
				}

				//**********************************************************
				//	ITEMNO�̕\���t���O���Z�b�g����
				//		input	item_no =	�區��No
				//				flg		=	[0:����,1:�J��]
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
					//	�߂�l
					//
					return ii;
				}

				//**********************************************************
				//	ITEMNO�̕\���t���O��Ԃ�
				//		input	item_no =	�區��No
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
					//	�߂�l
					//
					return iRet;
				}

				//**********************************************************
				//	���[�U�[�o�^�J�e�S���\��
				//**********************************************************
				function setUserCategory() {
					//
					//	�����g�p�ϐ���`
					//
					var	strCatTable = "";
					var ii;
					var iiMax=0;
					var jjMax=0;
					var	varTotal=0;
					var	varCount=0;
					var numAryDispList	= new Array();	//	�\����

					//
					//	�o�^�J�e�S���̌���������
					//
					jjMax=0;
					for(var jj in objAryUserItemID) {
						//	�\���������߂�ׂ̃C���f�b�N�X�쐬
						numAryDispList[jjMax] = jj;

						jjMax++;
						//	�o�^�J�e�S���ɊY���������ނɐF������O���
						for( ii=0; ii<iiCategoryMax; ii++ ) {
							if( strAryCategoryID[ii].substr(0,strAryCategoryID[ii].length) == jj.substr(0,strAryCategoryID[ii].length) ) {
								strAryCategoryCOL[ii] = 1;	//	�F�t
							}
						}
					}

					//	�\�[�g����
					for( var nn=0; nn<(jjMax-1); nn++ ) {
						for( var kk=nn+1; kk<jjMax; kk++ ) {
							if( varDisp6Flg==0 ) {
								//	�ŐV
								var varA = Number(objAryTotal[numAryDispList[nn]]);
								var varB = Number(objAryTotal[numAryDispList[kk]]);
							} else {
								//	�U����
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
						//	�o�^�J�e�S���Ȃ�
						//
						strCatTable += "<h2 class='h2_m_top01'>My�Č��ŐV���@�����Ȃ̓o�^�σJ�e�S���[�̈Č��������͂��������܂��B</h2>";
						////strCatTable += "<table class='tb-cateList'>";
						strCatTable += "<div class='memTop-cate-error'>";
						strCatTable += "<p class='notice_normal'>���݁A���o�^���������Ă���J�e�S���[�͂���܂���B</p>";
						strCatTable += "<p>&nbsp;</p>";
						strCatTable += "<p>���o�^�����������i�ڃJ�e�S���[�Ƀ}�b�`�����Č������͂�����T�[�r�X�ƂȂ��Ă���܂��̂�<br />";
						strCatTable += "�i�ڃJ�e�S���[�����o�^�̏ꍇ �Č��Ɋւ����̓I�Ȃ��ē������͂����邱�Ƃ��ł��܂���B</p>";

						////strCatTable += "<p class='mail-ad'>�i�ڃJ�e�S���[�o�^�Ɋւ��邨�⍇���F<a href='mailto:dem-info@deecorp.jp'>dem-info@deecorp.jp</a></p>";
						strCatTable += "<p>&nbsp;</p>";

						strCatTable += "</div>";
					} else {
						//
						//	�S���\���e�[�u���쐬
						//
						strCatTable += "<h2 class='h2_m_top01'>My�Č��ŐV���@�����Ȃ̓o�^�σJ�e�S���[�̈Č��������͂��������܂��B</h2>";

						//
						//	�^�u�\��
						//
						if( varDisp6Flg==0 ) {
							//	�ŐV
							strCatTable += "<table border='0' cellspacing='0' cellpadding='0' class='memlist-tab'>";
							strCatTable += "	<tr>";
							strCatTable += "	<td><img src='images/spacer.gif' width='5' height='24' /></td>";
							strCatTable += "	<td><img src='images/members/tab_memlast.gif' width='92' height='24' /></td>";
							strCatTable += "	<td><a class='btn-memtab6month' style='cursor: pointer;' onclick='varDisp6Flg=1; setUserCategory()'>6�����i�݌v�j</a></td>";
							strCatTable += "	<td><img src='images/spacer.gif' width='345' height='24' /></td>";
							strCatTable += "	</tr>";
							strCatTable += "	<tr>";
							strCatTable += "	<td colspan='4'><img src='images/members/tab_memline6month.gif' width='533' height='3' /></td>";
							strCatTable += "	</tr>";
							strCatTable += "</table>";
						} else {
							//	�ߋ�
							strCatTable += "<table border='0' cellspacing='0' cellpadding='0' class='memlist-tab'>";
							strCatTable += "	<tr>";
							strCatTable += "    <td><img src='images/spacer.gif' width='5' height='24' /></td>";
							strCatTable += "    <td><a class='btn-memtabLast' style='cursor: pointer;' onclick='varDisp6Flg=0; setUserCategory()'>�ŐV</a></td>";
							strCatTable += "    <td><img src='images/members/tab_mem6month.gif' width='92' height='24' /></td>";
							strCatTable += "    <td><img src='images/spacer.gif' width='345' height='24' /></td>";
							strCatTable += "    <tr>";
							strCatTable += "    <td colspan='4'><img src='images/members/tab_memlinelast.gif' width='533' height='3' /></td>";
							strCatTable += "    </tr>";
							strCatTable += "</table>";
						}
						strCatTable += addMsg+"<table class='tb-cateList'>";
						strCatTable += "<tr><th nowrap='nowrap'>&nbsp;</th><td class='td_amount'>���ψ˗����z�i�~�j</td><td class='td_number'>�Č�����</td></tr>";
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
							if( varTotal == 'undefined' ) varTotal = '0��';
							if( varCount == 'undefined' ) varCount = 0;
							if( varTotal == 'null' ) varTotal = '0��';
							if( varCount == 'null' ) varCount = 0;
							if( varTotal == "" ) varTotal = '0��';
							if( varCount == "" ) varCount = 0;

							var strShoItemName = objAryShoItemName[ii];
							if( varDisp6Flg==0 ) {
								//	�ŐV
								if( varTotal == '0��' && varCount == 0 ) {
									//	���z�O�@�����O
									strCatTable += "<tr><th nowrap='nowrap'>�E"+strShoItemName+"</th><td class='td_amount'>-</td><td class='td_number'>-</td></tr>";
								} else if( varTotal == '0��' ) {
									//	���z���Ȃ��Ƃ�
									if( varCount <= 4 ) {
										//	5������
										strCatTable += "<tr><th nowrap='nowrap'>�E<a onclick=\"window.open('<%=INI_url%>?item1="+objAryShoItemNo1[ii]+"&item2="+objAryShoItemNo2[ii]+"&item3="+objAryShoItemNo3[ii]+"', 'winName', 'left=0,top=0,width=600,height=600,status=0,scrollbars=1,menubar=0,location=0,toolbar=0,resizable=1');\" href='javascript:;'>"+strShoItemName+"</a></th><td class='td_amount'>���ݐώZ��</td><td class='td_number'>5������</td></tr>";
									} else {
										strCatTable += "<tr><th nowrap='nowrap'>�E<a onclick=\"window.open('<%=INI_url%>?item1="+objAryShoItemNo1[ii]+"&item2="+objAryShoItemNo2[ii]+"&item3="+objAryShoItemNo3[ii]+"', 'winName', 'left=0,top=0,width=600,height=600,status=0,scrollbars=1,menubar=0,location=0,toolbar=0,resizable=1');\" href='javascript:;'>"+strShoItemName+"</a></th><td class='td_amount'>���ݐώZ��</td><td class='td_number'>"+varCount+"</td></tr>";
									}
								} else {
									if( varCount <= 4 ) {
										//	5������
										strCatTable += "<tr><th nowrap='nowrap'>�E<a onclick=\"window.open('<%=INI_url%>?item1="+objAryShoItemNo1[ii]+"&item2="+objAryShoItemNo2[ii]+"&item3="+objAryShoItemNo3[ii]+"', 'winName', 'left=0,top=0,width=600,height=600,status=0,scrollbars=1,menubar=0,location=0,toolbar=0,resizable=1');\" href='javascript:;'>"+strShoItemName+"</a></th><td class='td_amount'>"+varTotal+"</td><td class='td_number'>5������</td></tr>";
									} else {
										strCatTable += "<tr><th nowrap='nowrap'>�E<a onclick=\"window.open('<%=INI_url%>?item1="+objAryShoItemNo1[ii]+"&item2="+objAryShoItemNo2[ii]+"&item3="+objAryShoItemNo3[ii]+"', 'winName', 'left=0,top=0,width=600,height=600,status=0,scrollbars=1,menubar=0,location=0,toolbar=0,resizable=1');\" href='javascript:;'>"+strShoItemName+"</a></th><td class='td_amount'>"+varTotal+"</td><td class='td_number'>"+varCount+"</td></tr>";
									}
								}
							} else {
								//	�ߋ��͏ڍׂ̃����N�Ȃ�
								if( varTotal == '0��' && varCount == 0 ) {
									//	���z�O�@�����O
									strCatTable += "<tr><th nowrap='nowrap'>�E"+strShoItemName+"</th><td class='td_amount'>-</td><td class='td_number'>-</td></tr>";
								} else if( varTotal == '0��' ) {
									//	���z���Ȃ��Ƃ�
									if( varCount <= 4 ) {
										//	5������
										strCatTable += "<tr><th nowrap='nowrap'>�E"+strShoItemName+"</a></th><td class='td_amount'>���ݐώZ��</td><td class='td_number'>5������</td></tr>";
									} else {
										strCatTable += "<tr><th nowrap='nowrap'>�E"+strShoItemName+"</a></th><td class='td_amount'>���ݐώZ��</td><td class='td_number'>"+varCount+"</td></tr>";
									}
								} else {
									if( varCount <= 4 ) {
										//	5������
										strCatTable += "<tr><th nowrap='nowrap'>�E"+strShoItemName+"</a></th><td class='td_amount'>"+varTotal+"</td><td class='td_number'>5������</td></tr>";
									} else {
										strCatTable += "<tr><th nowrap='nowrap'>�E"+strShoItemName+"</a></th><td class='td_amount'>"+varTotal+"</td><td class='td_number'>"+varCount+"</td></tr>";
									}
								}
							}
							iiMax++;
							if( iiMax >= 5 ) break;
						}
						strCatTable += "</table>";

						//	�S�Ă݂�{�^���̕\��
						strCatTable += "<p class='notice'>&nbsp;&nbsp;&nbsp;&nbsp;���u-�v�\���̃J�e�S���[�ɂ��܂��ẮA���݊Y���Č��͂������܂���B</p>";
						if( jjMax > 5 ) {
							strCatTable += "<div class='bBlock-memTop01-btm'><a href='./member_detail_user.jsp?varDisp6Flg="+varDisp6Flg+"' class='btn-memTop-seeall'>�S�Č���</a></div>";
						}
					}
					obj = document.getElementById("usetItemId");
					obj.innerHTML = strCatTable;
				}

				//**********************************************************
				//	�S���J�e�S���\��
				//		input	item_no =	�區��No
				//				flg		=	[0:����,1:�J��]
				//**********************************************************
				function setCategory(item_no,flg) {
					//
					//	�����g�p�ϐ���`
					//
					var	strCatTable = "";
					var ii;
					var	varTotal=0;
					var	varCount=0;

					//
					//	item_no����flg���Z�b�g����
					//
					setFlg(item_no,flg);

					strCatTable += "<h2 class='h2_m_top02'>�{���̍ŐV�Č����</h2>";
					strCatTable += "<p>���ρ�Dee�ɂ�����{���̍ŐV�Č��������͂����Ă���܂��B</p>";
					strCatTable += "<p>��<font color='#ff6600'>�F</font>�̂��Ă��鍀�ڂ́A���Ȃ��̓o�^�J�e�S���[�ł��B</p>";

					//
					//	�^�u�\��
					//
					if( varDisp6Flg2 == 0 ) {
						//	�ŐV
						strCatTable += "<table border='0' cellspacing='0' cellpadding='0' class='catelist-tab'>";
						strCatTable += "<tr>";
						strCatTable += "<td><img src='images/spacer.gif' width='5' height='24' /></td>";
						strCatTable += "<td><img src='images/members/tab_catelast.gif' width='92' height='24' /></td>";
						strCatTable += "<td><a class='btn-catetab6month' style='cursor: pointer;' onclick='varDisp6Flg2=1; setCategory("+item_no+","+flg+")'>6�����i�݌v�j</a></td>";
						strCatTable += "<td><img src='images/spacer.gif' width='325' height='24' /></td>";
						strCatTable += "</tr>";
						strCatTable += "<tr>";
						strCatTable += "<td colspan='4'><img src='images/members/tab_cateline6month.gif' width='513' height='3' /></td>";
						strCatTable += "</tr>";
						strCatTable += "</table>";
					} else {
						//	�ߋ�
						strCatTable += "<table border='0' cellspacing='0' cellpadding='0' class='catelist-tab'>";
						strCatTable += "<tr>";
						strCatTable += "<td><img src='images/spacer.gif' width='5' height='24' /></td>";
						strCatTable += "<td><a class='btn-catetabLast' style='cursor: pointer;' onclick='varDisp6Flg2=0; setCategory("+item_no+","+flg+")'>�{��</a></td>";
						strCatTable += "<td><img src='images/members/tab_cate6month.gif' width='92' height='24' /></td>";
						strCatTable += "<td><img src='images/spacer.gif' width='325' height='24' /></td>";
						strCatTable += "</tr>";
						strCatTable += "<tr>";
						strCatTable += "<td colspan='4'><img src='images/members/tab_catelinelast.gif' width='513' height='3' /></td>";
						strCatTable += "</tr>";
						strCatTable += "</table>";
					}

					//
					//	�S���\���e�[�u���쐬
					//
					strCatTable += "<div class='cateList'>";
					strCatTable += "<table class='tb-cateList'>";
					strCatTable += "<tr><th nowrap='nowrap'><td class='td_amount'>���ψ˗����z�i�~�j</td><td class='td_number'>�Č�����</td></tr>";
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
						if( varTotal == 'undefined' ) varTotal = '0��';
						if( varCount == 'undefined' ) varCount = 0;
						if( varTotal == 'null' ) varTotal = '0��';
						if( varCount == 'null' ) varCount = 0;
						if( varTotal == "" ) varTotal = '0��';
						if( varCount == "" ) varCount = 0;

						//
						//	�S���e�[�u���쐬
						//
						var	strCatName = "";
						if( strAryCategoryCOL[ii]==1 ) {
							strCatName = "<font color='#ff6600'>"+strAryCategory[ii]+"</font>";
						} else {
							strCatName = strAryCategory[ii];
						}

						if( strAryCategoryID[ii].length <= 3 ) {
							var strNowItem1 = strAryCategoryID[ii];

							//	�區�ڕ\��
							if( strAryCategoryFLG[ii] == 0 && varstrSelItem1 != strNowItem1 ) {
								//	���Ă���
								if( varTotal == '0��' && varCount == 0 ) {
									//	���z�O�@�����O
									strCatTable += "<tr><th nowrap='nowrap'><img src='./images/migi.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',1);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',1);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>-</td><td class='td_number'>-</td></tr>";
								} else if( varTotal == '0��' ) {
									//	���z���Ȃ��Ƃ�
									if( varCount <= 4 ) {
										//	5������
										strCatTable += "<tr><th nowrap='nowrap'><img src='./images/migi.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',1);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',1);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>���ݐώZ��</td><td class='td_number'>5������</td></tr>";
									} else {
										strCatTable += "<tr><th nowrap='nowrap'><img src='./images/migi.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',1);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',1);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>���ݐώZ��</td><td class='td_number'>"+varCount+"</td></tr>";
									}
								} else {
									if( varCount <= 4 ) {
										//	5������
										strCatTable += "<tr><th nowrap='nowrap'><img src='./images/migi.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',1);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',1);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>"+varTotal+"</td><td class='td_number'>5������</td></tr>";
									} else {
										strCatTable += "<tr><th nowrap='nowrap'><img src='./images/migi.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',1);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',1);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>"+varTotal+"</td><td class='td_number'>"+varCount+"</td></tr>";
									}
								}
							} else {
								//	�J���Ă���
								if( varstrSelItem1 == strNowItem1 ) {
									strAryCategoryFLG[ii] = 1;
									varstrSelItem1="";
								}
								if( varTotal == '0��' && varCount == 0 ) {
									//	���z�O�@�����O
									strCatTable += "<tr><th nowrap='nowrap'><img src='./images/shita.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',0);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',0);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>-</td><td class='td_number'>-</td></tr>";
								} else if( varTotal == '0��' ) {
									//	���z���Ȃ��Ƃ�
									if( varCount <= 4 ) {
										//	5������
										strCatTable += "<tr><th nowrap='nowrap'><img src='./images/shita.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',0);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',0);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>���ݐώZ��</td><td class='td_number'>5������</td></tr>";
									} else {
										strCatTable += "<tr><th nowrap='nowrap'><img src='./images/shita.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',0);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',0);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>���ݐώZ��</td><td class='td_number'>"+varCount+"</td></tr>";
									}
								} else {
									if( varCount <= 4 ) {
										//	5������
										strCatTable += "<tr><th nowrap='nowrap'><img src='./images/shita.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',0);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',0);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>"+varTotal+"</td><td class='td_number'>5������</td></tr>";
									} else {
										strCatTable += "<tr><th nowrap='nowrap'><img src='./images/shita.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',0);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,3)+"',0);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>"+varTotal+"</td><td class='td_number'>"+varCount+"</td></tr>";
									}
								}
							}
						} else if( strAryCategoryID[ii].length <= 6 ) {
							//	�����ڕ\��
							if( getFlg(strAryCategoryID[ii].substr(0,3)) == 1 ) {
								//	�����ނ��J���Ă�����
								if( getFlg(strAryCategoryID[ii].substr(0,6)) == 0 ) {
									//	�����ނ����Ă�����
									if( varTotal == '0��' && varCount == 0 ) {
										//	���z�O�@�����O
										strCatTable += "<tr><th nowrap='nowrap'>&nbsp;&nbsp;<img src='./images/migi.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',1);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',1);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>-</td><td class='td_number'>-</td></tr>";
									} else if( varTotal == '0��' ) {
										//	���z���Ȃ��Ƃ�
										if( varCount <= 4 ) {
											//	5������
											strCatTable += "<tr><th nowrap='nowrap'>&nbsp;&nbsp;<img src='./images/migi.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',1);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',1);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>���ݐώZ��</td><td class='td_number'>5������</td></tr>";
										} else {
											strCatTable += "<tr><th nowrap='nowrap'>&nbsp;&nbsp;<img src='./images/migi.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',1);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',1);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>���ݐώZ��</td><td class='td_number'>"+varCount+"</td></tr>";
										}
									} else {
										if( varCount <= 4 ) {
											//	5������
											strCatTable += "<tr><th nowrap='nowrap'>&nbsp;&nbsp;<img src='./images/migi.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',1);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',1);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>"+varTotal+"</td><td class='td_number'>5������</td></tr>";
										} else {
											strCatTable += "<tr><th nowrap='nowrap'>&nbsp;&nbsp;<img src='./images/migi.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',1);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',1);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>"+varTotal+"</td><td class='td_number'>"+varCount+"</td></tr>";
										}
									}
								} else {
									//	�����ނ��J���Ă�����
									if( varTotal == '0��' && varCount == 0 ) {
										//	���z�O�@�����O
										strCatTable += "<tr><th nowrap='nowrap'>&nbsp;&nbsp;<img src='./images/shita.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',0);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',0);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>-</td><td class='td_number'>-</td></tr>";
									} else if( varTotal == '0��' ) {
										//	���z���Ȃ��Ƃ�
										if( varCount <= 4 ) {
											//	5������
											strCatTable += "<tr><th nowrap='nowrap'>&nbsp;&nbsp;<img src='./images/shita.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',0);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',0);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>���ݐώZ��</td><td class='td_number'>5������</td></tr>";
										} else {
											strCatTable += "<tr><th nowrap='nowrap'>&nbsp;&nbsp;<img src='./images/shita.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',0);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',0);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>���ݐώZ��</td><td class='td_number'>"+varCount+"</td></tr>";
										}
									} else {
										if( varCount <= 4 ) {
											//	5������
											strCatTable += "<tr><th nowrap='nowrap'>&nbsp;&nbsp;<img src='./images/shita.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',0);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',0);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>"+varTotal+"</td><td class='td_number'>5������</td></tr>";
										} else {
											strCatTable += "<tr><th nowrap='nowrap'>&nbsp;&nbsp;<img src='./images/shita.gif' onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',0);\" style='cursor: pointer;'> <a name='A"+strNowItem1+"'><u onclick=\"setCategory('"+strAryCategoryID[ii].substr(0,6)+"',0);\" style='cursor: pointer;'>" +strCatName+"</u></a></th><td class='td_amount'>"+varTotal+"</td><td class='td_number'>"+varCount+"</td></tr>";
										}
									}
								}
							}
						} else {
							//	�����ڕ\��
							if( getFlg(strAryCategoryID[ii].substr(0,3)) == 1 ) {
								//	�����ނ��J���Ă���
								if( getFlg(strAryCategoryID[ii].substr(0,6)) == 1 ) {
									//	�J���Ă�����
									if( varCount > 0 ) {
										//	��������
										if( varDisp6Flg2==0 ) {
											//	�ŐV
											if( varTotal == '0��' && varCount == 0 ) {
												//	���z�O�@�����O
												strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;�E<a onclick=\"window.open('<%=INI_url%>?item1="+strAryCategoryID[ii].substr(0,3)+"&item2="+strAryCategoryID[ii].substr(0,6)+"&item3="+strAryCategoryID[ii]+"', 'winName', 'left=0,top=0,width=600,height=600,status=0,scrollbars=1,menubar=0,location=0,toolbar=0,resizable=1');\" href='javascript:;'>"+strCatName+"</a></td><td class='td_amount'>-</td><td class='td_number'>-</td></tr>";
											} else if( varTotal == '0��' ) {
												//	���z���Ȃ��Ƃ�
												if( varCount <= 4 ) {
													//	5������
													strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;�E<a onclick=\"window.open('<%=INI_url%>?item1="+strAryCategoryID[ii].substr(0,3)+"&item2="+strAryCategoryID[ii].substr(0,6)+"&item3="+strAryCategoryID[ii]+"', 'winName', 'left=0,top=0,width=600,height=600,status=0,scrollbars=1,menubar=0,location=0,toolbar=0,resizable=1');\" href='javascript:;'>"+strCatName+"</a></td><td class='td_amount'>���ݐώZ��</td><td class='td_number'>5������</td></tr>";
												} else {
													strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;�E<a onclick=\"window.open('<%=INI_url%>?item1="+strAryCategoryID[ii].substr(0,3)+"&item2="+strAryCategoryID[ii].substr(0,6)+"&item3="+strAryCategoryID[ii]+"', 'winName', 'left=0,top=0,width=600,height=600,status=0,scrollbars=1,menubar=0,location=0,toolbar=0,resizable=1');\" href='javascript:;'>"+strCatName+"</a></td><td class='td_amount'>���ݐώZ��</td><td class='td_number'>"+varCount+"</td></tr>";
												}
											} else {
												if( varCount <= 4 ) {
													//	5������
													strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;�E<a onclick=\"window.open('<%=INI_url%>?item1="+strAryCategoryID[ii].substr(0,3)+"&item2="+strAryCategoryID[ii].substr(0,6)+"&item3="+strAryCategoryID[ii]+"', 'winName', 'left=0,top=0,width=600,height=600,status=0,scrollbars=1,menubar=0,location=0,toolbar=0,resizable=1');\" href='javascript:;'>"+strCatName+"</a></td><td class='td_amount'>"+varTotal+"</td><td class='td_number'>5������</td></tr>";
												} else {
													strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;�E<a onclick=\"window.open('<%=INI_url%>?item1="+strAryCategoryID[ii].substr(0,3)+"&item2="+strAryCategoryID[ii].substr(0,6)+"&item3="+strAryCategoryID[ii]+"', 'winName', 'left=0,top=0,width=600,height=600,status=0,scrollbars=1,menubar=0,location=0,toolbar=0,resizable=1');\" href='javascript:;'>"+strCatName+"</a></td><td class='td_amount'>"+varTotal+"</td><td class='td_number'>"+varCount+"</td></tr>";
												}
											}
										} else {
											//	�ߋ��͏ڍׂ̃����N�Ȃ�
											if( varTotal == '0��' && varCount == 0 ) {
												//	���z�O�@�����O
												strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;�E"+strCatName+"</a></td><td class='td_amount'>-</td><td class='td_number'>-</td></tr>";
											} else if( varTotal == '0��' ) {
												//	���z���Ȃ��Ƃ�
												if( varCount <= 4 ) {
													//	5������
													strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;�E"+strCatName+"</a></td><td class='td_amount'>���ݐώZ��</td><td class='td_number'>5������</td></tr>";
												} else {
													strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;�E"+strCatName+"</a></td><td class='td_amount'>���ݐώZ��</td><td class='td_number'>"+varCount+"</td></tr>";
												}
											} else {
												if( varCount <= 4 ) {
													//	5������
													strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;�E"+strCatName+"</a></td><td class='td_amount'>"+varTotal+"</td><td class='td_number'>5������</td></tr>";
												} else {
													strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;�E"+strCatName+"</a></td><td class='td_amount'>"+varTotal+"</td><td class='td_number'>"+varCount+"</td></tr>";
												}
											}
										}
									} else {
										//	�����Ȃ�
										if( varTotal == '0��' && varCount == 0 ) {
											//	���z�O�@�����O
											strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;�E"+strCatName+"</a></td><td class='td_amount'>-</td><td class='td_number'>-</td></tr>";
										} else if( varTotal == '0��' ) {
											//	���z���Ȃ��Ƃ�
											if( varCount <= 4 ) {
												//	5������
												strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;�E"+strCatName+"</a></td><td class='td_amount'>���ݐώZ��</td><td class='td_number'>5������</td></tr>";
											} else {
												strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;�E"+strCatName+"</a></td><td class='td_amount'>���ݐώZ��</td><td class='td_number'>"+varCount+"</td></tr>";
											}
										} else {
											if( varCount <= 4 ) {
												//	5������
												strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;�E"+strCatName+"</a></td><td class='td_amount'>"+varTotal+"</td><td class='td_number'>5������</td></tr>";
											} else {
												strCatTable += "<tr><td class='td-subList'>&nbsp;&nbsp;�E"+strCatName+"</a></td><td class='td_amount'>"+varTotal+"</td><td class='td_number'>"+varCount+"</td></tr>";
											}
										}
									}
								}
							}
						}
					}
					strCatTable += "</table>";
					strCatTable += "<p class='notice'>&nbsp;&nbsp;&nbsp;&nbsp;���u-�v�\���̃J�e�S���[�ɂ��܂��ẮA���݊Y���Č��͂������܂���B</p>";
					strCatTable += "</div>";
					obj = document.getElementById("catListAll");
					obj.innerHTML = strCatTable;
				}
		//-->
		</script>
	</head>

<!-- �ŐV�������������Ȃ�(09/01/08)
	<body onload="setUserCategory(); setCategory();" oncontextmenu="return false"><a name="pagetop" id="pagetop"></a>
-->
	<body oncontextmenu="return false"><a name="pagetop" id="pagetop"></a>
		<div id="wrapper" class="biz2nd">
			<!-- header -->
			<div id="header">
				<p id="logo-biz"><a href="http://bb.deecorp.jp"><img src="../images/biz/biz_logo.gif" alt="DeeCorp" width="140" height="28" border="0"></a></p>
				<ul id="hd-submenu">
					<li><a href="https://www2.deecorp.jp/dee-hp/contact_input.jsp"><img src="../images/btn_contact.gif" alt="���₢���킹" width="62" height="10" border="0" /></a></li>
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
					<div style="padding-bottom:5px;"><img src="images/members/keyVisual.jpg" alt="Business@DeeBank�����p�y�[�W" width="535" height="170" /></div>
					<p class="loginMessage">������Dee�T�[�r�X�������p���������A���肪�Ƃ��������܂��B</p>
	                <table class="loginStatus">
	                	<tr>
	                    <td width="1%"><img src="images/members/icon_mem.gif" alt="*" width="19" height="19" /></td>
						<td><%=user_name%>�@�l&nbsp;&nbsp;&nbsp;&nbsp;<%=strLoginDate%> ����</td>
	                    <td width="10%"><a href="./member_logout.jsp"><img src="images/members/btn_mem_logout.gif" alt="���O�A�E�g" width="82" height="23" border="0" /></a></td>
						</tr>
	                </table>

<!-- ����@Dee���j���[�A���ɂƂ��Ȃ��ύX�ӏ��i���̂Q�j��������i09/01/07�j -->
                
<img src="images/members/renewal/img_renewalTop.jpg" alt="����@Dee���j���[�A���I" border="0" usemap="#img_renewalTopMap" id="img_renewalTop" />
<map name="img_renewalTopMap" id="img_renewalTopMap">
  <area shape="rect" coords="185,217,350,275" href="http://www2.deecorp.jp/dee-tutorial/" target="_blank" alt="����@Dee�̌���" />
  <area shape="rect" coords="362,217,525,275" href="./renewal.html" alt="����@Dee���j���[�A���T�v" />
<area shape="rect" coords="8,217,174,275" href="https://with.deecorp.jp/dee/dem2/" target="_blank" />
</map>

<!-- ����@Dee���j���[�A���ɂƂ��Ȃ��ύX�ӏ��i���̂Q�j�����܂Łi09/01/07�j -->
	                
<!-- �ŐV�������������Ȃ�(09/01/08)
	                <div id="usetItemId" name="usetItemId" class="bBlock-memTop01"></div>
					<div id="catListAll" name="catListAll" class="bBlock-memTop02"></div>
-->
	                <DIV class=backto-top><A href="#pagetop"><IMG alt=�y�[�W�g�b�v�ɖ߂� src="../images/btn_backtotop.gif"></A></DIV>
			  	</div>
				<!-- column-left -->
				<div id="clm-left">

<!-- �C���ӏ���������F�����j���[�̉摜���ɂƂ��Ȃ��^�O�C���i�����N��͌���̏����g�p���Ă��������j -->

<!-- ����@Dee���j���[�A���ɂƂ��Ȃ��ύX�ӏ��i���̂P�j��������i09/01/07�j -->
            
				<div id="regionalMenu">
					<p><img src="images/members/rmenu_mem.gif" alt="�e��T�[�r�X���O�C��" width="165" height="50" /></p>
					<ul id="rgmenu_mem">
						<li class="rg1"><a href="https://with.deecorp.jp/dee/dem2/" target="_blank">����@Dee</a></li>
						<li class="rg3"><a href="https://with.deecorp.jp/dee/dsp/pcapp/" target="_blank">�Č��C���f�b�N�X�@�\</a></li>
						<li class="rg4"><a href="http://www2.deecorp.jp/dee-tutorial/" target="_blank">����@Dee���j���[�A���̌���</a></li>
                        <li class="rg5"><a href="./renewal.html">����@Dee���j���[�A���T�v</a></li>
                        <li class="rg8"><a href="https://with.deecorp.jp/dee/ptl2/Query.action" target="_blank">�悭���鎿��</a></li>
					</ul>
				</div>
                
                <p>��<a href="http://bb.deecorp.jp/important/keiyaku081217.html" target="_blank">�_��@Dee Free��</a><br />�@<a href="http://bb.deecorp.jp/important/keiyaku081217.html" target="_blank">�T�[�r�X�I���ɂ���</a></p>
                
<!-- ����@Dee���j���[�A���ɂƂ��Ȃ��ύX�ӏ��i���̂P�j�����܂Łi09/01/07�j -->
                
<!-- �C���ӏ������܂ŁF�����j���[�̉摜���ɂƂ��Ȃ��^�O�C�� -->
			  </div>
			</div>
			<!-- footer -->
			<p id="utility-footer"><a href="http://www.deecorp.jp/utility/security.html" target="_blank">�Z�L�����e�B��{���j</a>�@�b�@<a href="http://www.deecorp.jp/utility/privacy2.html" target="_blank">�l���ی���j</a>�@�b�@<a href="http://www.deecorp.jp/utility/privacy.html" target="_blank">�l���̎�舵��</a></p>
			<p id="footer-corp">�f�B�[�R�[�v������� / �������̓\�t�g�o���N�O���[�v�ł�</p>
			<p id="copyright">Copyright &copy; 2008 DeeCorp. All rights reserved.</p>
		</div>

		<script language="JavaScript">
		<!--//
			//
			//	�����J�e�S���e�[�u���쐬
			//
			<%
/* �ŐV�������������Ȃ�(09/01/08)
			int iNowCategoryMax=0;
			int jj=0;
			for( int i=0; i<iCategoryMax; i++ ) {
				if( !strAryCategoryID[i].equals("800") ) {		//	���ʎd���͕\���Ȃ�
					if( !strAryCategoryID[i].equals("900") ) {	//	�V���b�v�Ǘ��p�͕\���Ȃ�
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

			//	TOTAL(6����)
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

			//	COUNT(6����)
			Object[] objKey26=objAryCount6.keySet().toArray();
			for(int i=0;i<objKey26.length;i++){
				out.print("objAryCount6['"+objKey26[i]+"']='"+objAryCount6.get(objKey26[i])+"'; ");
			}

			//	USER ITEM
			Object[] objKey3=objAryUserItemID.keySet().toArray();
			for(int i=0;i<objKey3.length;i++){
				String strDumobjKey3 = (String)objKey3[i];
				if( strDumobjKey3.substring(0,3).equals("800") ) {
					//	���ʎd����͕\�����Ȃ�
				} else if( strDumobjKey3.substring(0,3).equals("900") ) {
					//	�V���b�v�Ǘ��p�͕\�����Ȃ�
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
	//	DB�ؒf
	//**********************************************************************************************
	//	�f�[�^�x�[�X����ؒf
	stmt.close();
	//	�ڑ��N���[�Y
	db.close();
	//	�f�[�^�x�[�X����ؒf
	stmt2.close();
	//	�ڑ��N���[�Y
	db2.close();
%>
