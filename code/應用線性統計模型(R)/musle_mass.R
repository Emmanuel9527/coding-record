#8.4
#(a)
data <- read.table(file = "C:/Users/User/OneDrive/Desktop/應用線性統計模型作業/musle_mass.txt", 
header=FALSE, col.names = c('m_mass', 'age'))
attach(data)
age_m <- age-mean(age)
model <- lm(m_mass ~ age_m + I(age_m^2))
#畫圖
library(ggplot2)
ggplot(data, aes(x = age_m, y = m_mass)) +
  geom_point() +
  stat_smooth(method = "lm", formula = y ~ x + I(x^2), se = FALSE) +
  labs(title = "2nd order linear regression", x = "age_m", y = "m_mass")
summary(model)

#(c)求95%CI at x=48
new_data <- data.frame(age = 48)
prediction <- predict(model, newdata = new_data, interval = "confidence", level = 0.95)
print(prediction)

#(d)求95%PI at x=48
new_data <- data.frame(age = 48)
prediction <- predict(model, newdata = new_data, interval = "prediction", level = 0.95)
print(prediction)

#(g)求X與X^2,以及中心化之後之相關係數
age_2 <- age^2
cov_X <- cov(age,age_2)
std_age <- sd(age)
std_age_2 <- sd(age_2)
coef <- cov_X/(std_age*std_age_2)
print(coef)

age_m_2 <- age_m^2
cov_x <- cov(age_m,age_m_2)
std_age_m <- sd(age_m)
std_age_m_2 <- sd(age_m_2)
coef_x <- cov_x/(std_age_m*std_age_m_2)
print(coef_x)


#8.16
#(b)
data_gpa <- read.table(file="C:/Users/User/OneDrive/Desktop/應用線性統計模型作業/merge.txt", 
header=FALSE, col.names = c('GPA','ACT_test','major'))
attach(data_gpa)
model_gpa <- lm(GPA~ACT_test+major)
#(d)
res <- residuals(model_gpa)
cross <- ACT_test*major
plot(cross, res, xlab = "X1 * X2", ylab = "Residuals")
abline(h = 0, col = "red")

#8.20
#(a)
model_gpa_cross <- lm(GPA ~ ACT_test + major + cross)



