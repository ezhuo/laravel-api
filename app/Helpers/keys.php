<?php

const AUTH_ENABLED = true;

const DB_PREFIX = "";
const DB_PAGE_SIZE = 20; //分页大小
const DB_CREATED_AT = 'created_at';
const DB_UPDATED_AT = 'updated_at';
const DB_ORG_ID = 'org_id';

const TOKEN_EXPIRE = 60 * 60 * 3;//3小时
const TOKEN_WEIXIN_EXPIRE = 60 * 60 * 24 * 30;//30天
const CACHE_EXPIRE = 60 * 24;// 单位是分钟 ，24小时

const APP_CANTON_ID = 1187;
const APP_CANTON_FDN = '0003520.0001133.0001187.';
const APP_CANTON_NAME = '';

const APP_CANTON_LENGTH = 7;
const APP_PERPAGE = 15;
const APP_UPLOAD_DRIVER = "uploads";
const APP_TOKEN_CODE = 'EZHUO@2017';
const APP_REQUEST_CHECK_CODE = 'ezhuo@20161016';
const APP_PASSWORD_CODE = 'ezhuo@cn';

const EXPORT_HTTP_CODE = ['exports', 'exports2'];
const EXPORT_MAX_COUNT = 20001;
const TREE_HTTP_CODE = 'tree';

//通过URL验证请求列表
const REQUEST_URL_AUTH_LIST = EXPORT_HTTP_CODE;
//不验证请求列表
const REQUEST_NO_AUTH_LIST = ['download'];
// ---------------------------------------------------------

/**
 * 200 OK - [GET]：服务器成功返回用户请求的数据，该操作是幂等的（Idempotent）。
 */
const HTTP_OK = 200;
/**
 *201 CREATED - [POST/PUT/PATCH]：用户新建或修改数据成功。
 */
const HTTP_CREATE_OK = 201;
/**
 * 202 (SC_ACCEPTED)告诉客户端请求正在被执行，但还没有处理完。
 */
const HTTP_CREATE = 202;
/**
 *状态码203 (SC_NON_AUTHORITATIVE_INFORMATION)是表示文档被正常的返回，但是由于正在使用的是文档副本所以某些响应头信息可能不正确。这是 HTTP 1.1中新加入的。
 */
const HTTP_CANNOT = 203;
/**
 *204 NO CONTENT - [DELETE]：用户删除数据成功。
 */
CONST HTTP_NORESPONSE = 204;
/**
 * 重置内容205 (SC_RESET_CONTENT)的意思是虽然没有新文档但浏览器要重置文档显示。这个状态码用于强迫浏览器清除表单域。这是 HTTP 1.1中新加入的。
 */
CONST HTTP_DONE = 205;


/**
 *400 INVALID REQUEST - [POST/PUT/PATCH]：用户发出的请求有错误，服务器没有进行新建或修改数据的操作，该操作是幂等的。
 */
CONST HTTP_WRONG = 400;
/**
 *401 Unauthorized - [*]：表示用户没有权限（令牌、用户名、密码错误）。
 */
CONST HTTP_NOLOGIN = 401;
/**
 *403 Forbidden - [*] 表示用户得到授权（与401错误相对），但是访问是被禁止的。
 */
CONST HTTP_NOAUTH = 403;
/**
 *404 NOT FOUND - [*]：用户发出的请求针对的是不存在的记录，服务器没有进行操作，该操作是幂等的。
 */
CONST HTTP_NOFOUND = 404;
/**
 *406 Not Acceptable - [GET]：用户请求的格式不可得（比如用户请求JSON格式，但是只有XML格式）。
 */
CONST HTTP_NOTACCEPT = 406;
/**
 *410 Gone -[GET]：用户请求的资源被永久删除，且不会再得到的。
 */
CONST HTTP_DELETED = 410;
/**
 * 表示服务器不能处理请求（假设为带有附件的POST请求），除非客户端发送Content-Length头信息指出发送给服务器的数据的大小。该状态是新加入 HTTP 1.1的。
 */
CONST HTTP_NOLENGTH = 411;
/**
 * 状态指出请求头信息中的某些先决条件是错误的。该状态是新加入 HTTP 1.1的。
 */
CONST HTTP_NOCOND = 412;
/**
 *422 Unprocesable entity - [POST/PUT/PATCH] 当创建一个对象时，发生一个验证错误。
 */
CONST HTTP_VALIDATE = 422;
/**
 *500 INTERNAL SERVER ERROR - [*]：服务器发生错误，用户将无法判断发出的请求是否成功。
 */
CONST HTTP_ERROR = 500;

const HTTP_OK_MESSAGE = '操作成功';
const HTTP_OK_LOGINMSG = '登录成功';
const HTTP_OK_UPDATEMSG = '修改成功';
const HTTP_CREATE_MESSAGE = '已创建';
const HTTP_NORESPONSE_MESSAGE = '没有响应信息';
const HTTP_CANNOT_MESSAGE = '不能执行该操作';
const HTTP_DONE_MESSAGE = '已处理';
const HTTP_WRONG_MESSAGE = '请求错误';
const HTTP_WRONG_NOFOUND = '数据不存在';
const HTTP_NOLOGIN_MESSAGE = '此帐号有可能在其它设备登录，系统将在2秒后退出！';
const HTTP_WEIXIN_NOLOGIN_MESSAGE = '此帐号未登录';
const HTTP_NOLOGIN_LOGINMSG = '登录失败';
const HTTP_NOAUTH_MESSAGE = '无权限';
const HTTP_NOFOUND_MESSAGE = '找不到请求';
const HTTP_NOTACCEPT_MESSAGE = '请求的数据包无效';
const HTTP_DELETED_MESSAGE = '此数据已被删除,无法展示';
const HTTP_VALIDATE_MESSAGE = '您的操作，未通过服务端验证，操作无效！';