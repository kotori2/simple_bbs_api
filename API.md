接口说明：
以下接口发送和接受都是json格式。

/login 登录
提交：
name: 用户名
password: 密码
返回：
success: 是否登录成功
user_id: 用户ID，int
session: 类似于cookie的，记录用户登录信息的数据
expire: session的过期时间，UNIX时间戳
发生错误时：
success: false
message: 错误信息

/login 注册
提交：
name: 用户名
password: 密码
返回：
success: 是否注册成功
user_id: 用户ID，int
name: 跟提交的昵称一样
session: 类似于cookie的，记录用户登录信息的数据
expire: session的过期时间，UNIX时间戳
发生错误时：
success: false
message: 错误信息

【登录以后的header必须包含User-ID和Session】
/post 发帖
提交：
title: 帖子标题
content: 帖子内容
返回：
success: 是否注册成功
pid: 帖子id

/post_list 帖子列表
提交：
limit: 返回的最大帖子数量
返回：
success: 是否查询成功
posts: 数组，帖子列表
[pid: 帖子ID(int)
user_id: 发帖人ID(int)
title: 帖子标题
insert_date: 发帖时间
update_date: 最后回复时间
user_name: 发帖人名字
summary: 帖子内容摘要]

/post_list 帖子详情
提交：
pid: 帖子ID
page: 页码，int，0开始
返回：
success: 是否查询成功
post: 键值数组，帖子详情
[pid: 帖子ID(int)
user_id: 发帖人ID(int)
title: 帖子标题
content: 帖子内容
insert_date: 发帖时间
update_date: 最后回复时间
user_name: 发帖人名字]
replies: 键值数组，帖子回复
[rid: 回复ID(int)，1开始，计算楼层数请将该值+1
user_id: 回复人ID(int)
content: 回复内容
to_rid: 回复某楼层
insert_date: 回复时间
user_name: 回复人名字]

/reply 回复
提交:
pid: 帖子ID
content: 内容
返回:
success: 是否回复成功
rid: 回复ID