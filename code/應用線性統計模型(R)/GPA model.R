GPA.data <- read.table(file = "C:/Users/User/Downloads/gradepointaverage.txt",header=FALSE,col.names=c("GPA","ACT_score"))
attach(GPA.data)
model <- lm(GPA ~ ACT_score)
modelCI<-confint(model,level=0.99)

#b1假設檢定(H0:b1=0)
#計算p-value
p_value <- 2 * pt(-abs(summary(model)$coef[2,3]), 118)


ablime
# 使用 predict() 函數計算預測值及信心區間
prediction <- predict(model, newdata = data.frame(ACT_score = 28), interval = "confidence", level = 0.95)

# 提取信心區間的下限和上限
lower_bound <- prediction[1]
upper_bound <- prediction[2]

# 顯示結果
print(paste("ACT 分數為 28 的學生的平均新生 GPA 的 95% 信心區間的下限:", lower_bound))
print(paste("ACT 分數為 28 的學生的平均新生 GPA 的 95% 信心區間的上限:", upper_bound))


#Mary Jones ACT_score=28
# 使用 predict() 函數計算預測值及預測區間
prediction <- predict(model, newdata = data.frame(ACT_score = 28), interval = "prediction", level = 0.95)

# 提取預測區間的下限和上限
lower_bound <- prediction[1]
upper_bound <- prediction[2]



#confidance band
# 計算預測值 Y_h
Y_h <- predict(model, newdata = data.frame(ACT_score = 28))

# 提取 Y_h 的標準誤差
Y_var<-(sum((resid(model))^2))/118
X_mean<-mean(ACT_score)
Yh_var<-Y_var*(1/120+(28-X_mean)^2/sum((ACT_score-X_mean)^2))
Yh_std<-sqrt(Yh_var)

# 計算 F 分佈的乘數，取決於所選的信心水準和迴歸自由度
alpha <- 0.05  # 信心水準
df <- 118  # 迴歸自由度
W <- 2*qf(1 - alpha, df, 2)

# 計算信賴帶的邊界值
lower_bound <- Y_h - Yh_std * sqrt(W)
upper_bound <- Y_h + Yh_std * sqrt(W)

# 顯示結果
print(paste("X=28 時迴歸線的 95% 信賴帶的下界:", lower_bound))
print(paste("X=28 時迴歸線的 95% 信賴帶的上界:", upper_bound))









