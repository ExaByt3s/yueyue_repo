#无内容/断网组件

---


### 参数说明



 - `broken_network` - 是否断网，可不传，值：是 `1`，否 `0`（不传即为无内容）
 - `content_height` - 视图高度，必传，值：所需占满区域的高度（用于居中）



 ### 绑定事件说明


 - `tap:broken_network` - 断网时屏幕点击事件

 ```javascript

 abnormal_view.on('tap:broken_network',function(){
                            //to do
                        })
                        
 ```
 
