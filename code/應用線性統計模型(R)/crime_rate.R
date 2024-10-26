crime.data <- read.table(file = "C:/Users/User/Downloads/CH01PR28.txt",header=FALSE,col.names=c("crime_rate","diploma"))
attach(crime.data)
model <- lm(crime_rate~diploma)
summary(model)

%計算b1的99信賴區間
confint(model, parm = "diploma", level = 0.99)

%ANOVA
anova(model)

%皮爾遜相關係數
cor(crime_rate,diploma)

%bivariate normal dis. 獨立性檢定
r <- cor(crime_rate,diploma)
n <- nrow(crime.data)
t_test <- r/((1-r^2)/(n-2))^(1/2)
p_value <- 2 * (1 - pt(abs(t_test), n-2))
