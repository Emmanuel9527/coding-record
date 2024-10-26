install.packages("openxlsx")
library(openxlsx)
install.packages("leaps")
library(leaps)
library(ggplot2)

web.data <- read.table(file = "C:/Users/User/OneDrive/Desktop/應用線性統計模型作業/final/data06.txt", header=FALSE, col.names = c('ID', 'Webs_num', 'Backlog', 'Team', 'Team_exp', 'Proc_chg', 'Year', 'Quater'));
attach(web.data);
 
#處理類別資料(Quater與Year)
web.data$Q_1 <- ifelse(web.data$Quater == 1, 1, 0);
web.data$Q_1 <- as.integer(web.data$Q_1);
web.data$Q_2 <- ifelse(web.data$Quater == 2, 1, 0);
web.data$Q_2 <- as.integer(web.data$Q_2);
web.data$Q_3 <- ifelse(web.data$Quater == 3, 1, 0);
web.data$Q_3 <- as.integer(web.data$Q_3);
web.data$Q_4 <- ifelse(web.data$Quater == 4, 1, 0);
web.data$Q_4 <- as.integer(web.data$Q_4);

web.data$Y_2001 <- ifelse(web.data$Year == 2001, 1, 0);
web.data$Y_2001 <- as.integer(web.data$Y_2001);
web.data$Y_2002 <- ifelse(web.data$Year == 2002, 1, 0);
web.data$Y_2002 <- as.integer(web.data$Y_2002);

#將(Quater,Year)同時考慮，變為一個新的predictor
web.data$X1 <- ifelse(web.data$Q_1 == 1 & web.data$Y_2001 == 1, 1, 0)
web.data$X2 <- ifelse(web.data$Q_1 == 1 & web.data$Y_2002 == 1, 1, 0)
web.data$X3 <- ifelse(web.data$Q_2 == 1 & web.data$Y_2001 == 1, 1, 0)
web.data$X4 <- ifelse(web.data$Q_2 == 1 & web.data$Y_2002 == 1, 1, 0)
web.data$X5 <- ifelse(web.data$Q_3 == 1 & web.data$Y_2001 == 1, 1, 0)
web.data$X6 <- ifelse(web.data$Q_3 == 1 & web.data$Y_2002 == 1, 1, 0)
web.data$X7 <- ifelse(web.data$Q_4 == 1 & web.data$Y_2001 == 1, 1, 0)
web.data$X8 <- ifelse(web.data$Q_4 == 1 & web.data$Y_2002 == 1, 1, 0)

#處理類別資料(Team,1~5一組,6~10一組,11~13一組)
web.data$Team <- as.factor(web.data$Team)
levels(web.data$Team) <- list(G_1 = c("1", "2", "3", "4", "5"), 
G_2 = c("6", "7", "8", "9", "10"),G_3 = c("11", "12", "13"))
web.data$G1 <- ifelse(web.data$Team == "G_1" , 1, 0)
web.data$G2 <- ifelse(web.data$Team == "G_2" , 1, 0)
web.data$G3 <- ifelse(web.data$Team == "G_3" , 1, 0)

webs.data <- web.data[,-which(names(web.data) %in% c("ID","Quater", "Year","Team","Q_1","Q_2","Q_3","Q_4","Y_2001","Y_2002","X8"))]
pdf(file='C:/Users/User/OneDrive/Desktop/應用線性統計模型作業/final/scatter_plot.pdf')
pairs(webs.data)
dev.off()

cor_matrix=cor(webs.data)
cor_matrix <- as.data.frame(cor_matrix)


excel_file <- 'C:/Users/User/OneDrive/Desktop/應用線性統計模型作業/final/correlation_matrix.xlsx'
write.xlsx(cor_matrix, file = excel_file)
excel_file2 <- 'C:/Users/User/OneDrive/Desktop/應用線性統計模型作業/final/data.xlsx'
write.xlsx(webs.data, file = excel_file2)


y=webs.data$Webs_num
model=lm(y ~ Backlog+Team+Team_exp+Proc_chg+X1+X2+X3+X4+X5+X6+X7+G1+G2+G3,data=webs.data)
summary(model)

#因X6,X7,G3的係數算出來為NAN，推測其原因為multicolinear
model_2=lm(y ~ Backlog+Team+Team_exp+Proc_chg+X1+X2+X3+X4+X5+G1+G2,data=webs.data)

#lnY之模型
webs.data$Webs_num=webs.data$Webs_num+1 #因有0，+1才能取ln
y=webs.data$Webs_num
model_ln <- lm(log(y) ~ Backlog + Team + Team_exp + Proc_chg + X1 + X2 + X3 + X4 + X5+ G1 + G2, data = webs.data)
pdf(file = 'C:/Users/User/OneDrive/Desktop/應用線性統計模型作業/final/residual_qq_plots.pdf')
par(mfrow = c(2, 2))

residuals <- resid(model)
residuals_ln <- resid(model_ln)

#Y之residual plot
plot(fitted(model_2), residuals, main = "Residuals vs Fitted (Y)", xlab = "Fitted Values", ylab = "Residuals")
abline(h = 0, col = "red")

#Y之QQplot
qqnorm(residuals, main = "QQ Plot (Y)")
qqline(residuals, col = "red")

#ln(Y)之residual plot
plot(fitted(model_ln), residuals_ln, main = "Residuals vs Fitted (ln(Y))", xlab = "Fitted Values", ylab = "Residuals")
abline(h = 0, col = "red")

#ln(Y)之QQplot
qqnorm(residuals_ln, main = "QQ Plot (ln(Y))")
qqline(residuals_ln, col = "red")

dev.off()

#目前來看，應該以ln(Y)作為考慮，才較符合常態假設，且用一階線性模型應該是足夠的
#考慮有交互項之model(Backlog*Team_exp,Backlog*G1,Backlog*G2)
model_interaction <- lm(log(y) ~ Backlog:Team_exp+ Backlog:G1+Backlog:G2+Backlog+Team_exp + Proc_chg + X1 + X2 + X3 + X4 + X5 + G1+ G2, data = webs.data)
summary(model_interaction)

#開始訓練模型
data_split <- "C:/Users/User/OneDrive/Desktop/應用線性統計模型作業/final/data_split.xlsx"
data_train <- read_excel(data_split, sheet = 1)
y_train <- data_train[[c('Webs_num')]]
x_train <- data_train[,c('Backlog','Team_exp','Proc_chg','X1','X2','X3','X4','X5','G1','G2')]
# 調整資料
y_train=y_train+1
x_train$'Backlog_Team_exp' <- x_train$Backlog * x_train$Team_exp
x_train$'Backlog_G1' <- x_train$Backlog * x_train$G1
x_train$'Backlog_G2' <- x_train$Backlog * x_train$G2

# best subset algo
model_subset <- regsubsets(log(y_train) ~ Backlog_Team_exp+ Backlog_G1+Backlog_G2+Backlog+Team_exp + Proc_chg +X1 + X2 + X3 + X4 + X5 + G1+ G2,
data = x_train, nvmax = 13)  
summary_subset <- summary(model_subset)

sse <- summary_subset$rss
rsq <- summary_subset$rsq  # R^2
adj_rsq <- summary_subset$adjr2  # adjusted R^2
cp <- summary_subset$cp  # Cp 
aic <- summary_subset$aic  # AIC ，似乎沒有
bic <- summary_subset$bic  # BIC 

#test
if(FALSE){
temp <- names(which(summary_subset$which[9, ] )) #p=i時用到的predictor
predictors<- temp[!grepl("(Intercept)", temp, ignore.case = TRUE)]
x_selected <- as.matrix(x_train[, predictors])    

x_loo <- x_selected[-1, , drop = FALSE] #去掉第j筆觀測值，用剩下$
y_loo <- y_train[-1]

model_loo <- lm(paste("y_loo ~", paste(predictors, collapse = ' + ')),data=data.frame(x_loo))

x_test <- x_selected[1, , drop = FALSE]
new_data <- data.frame(x_test)
y_pred <- predict(model_loo, new_data)
(y_train[1] - y_pred)^2
}


# 沒有PRESS_P，自行計算，定義以下function
calculate_press <- function(x_train, y_train, model_subset, summary_subset) {
  n <- nrow(x_train)
  p <- ncol(x_train)
  press_values <- numeric(length = p)


  for (i in 1:p) {
    temp <- names(which(summary_subset$which[i, ] )) #p=i時用到的predictor
    predictors<- temp[!grepl("(Intercept)", temp, ignore.case = TRUE)]
    x_selected <- as.matrix(x_train[, predictors])
    
    press <- 0
    for (j in 1:n) {
      x_loo <- x_selected[-j, , drop = FALSE] #去掉第j筆觀測值，用剩下的資料來fit model
      y_loo <- y_train[-j]
      
      print(predictors)
      model_loo <- lm(paste("y_loo ~", paste(predictors, collapse = ' + ')),data=data.frame(x_loo))
      
      x_test <- x_selected[j, , drop = FALSE]
      new_data <- data.frame(x_test)
      y_pred <- predict(model_loo, new_data)
      
      press = press + (y_train[j] - y_pred)^2 #相當於summation
    }
    press_values[i] <- press
  }
  
  return(press_values)
}

#計算PRESS_P
press_p <- calculate_press(x_train, y_train, model_subset, summary_subset)





#將criterion儲存起來
stats_df <- data.frame(
  Number_of_Predictors = 1:length(rsq),
  SSEp = sse,
  Rsq = rsq,
  Adjusted_Rsq = adj_rsq,
  Cp = cp,
  SBC = bic,
  PRESS_P = press_p
)
criterion_xlsx <- 'C:/Users/User/OneDrive/Desktop/應用線性統計模型作業/final/criterion.xlsx'
write.xlsx(stats_df, file = criterion_xlsx,sheetName = 'criterion')
summary_df <- as.data.frame(summary_subset$which)
wb <- loadWorkbook(criterion_xlsx)
addWorksheet(wb, "summary") 
writeData(wb, "summary", summary_df) 
saveWorkbook(wb, criterion_xlsx, overwrite = TRUE)


#顯示criterion
if(FALSE){
print("SSE:")
print(sse)
print("R² values:")
print(rsq)
print("Adjusted R² values:")
print(adj_rsq)
print("Cp values:")
print(cp)
print("AIC values:")#為null
print(aic)
print("BIC values:")
print(bic)
print("PRESS values:")
print(press)}



# 繪製criterion圖表
criterion_pdf <- 'C:/Users/User/OneDrive/Desktop/應用線性統計模型作業/final/criterion_plots.pdf'
pdf(file = criterion_pdf)

par(mfrow = c(2, 3)) 

# R^2
plot(rsq, xlab = "Number of Predictors", ylab = "R^2", type = "b", col = "blue")
title(main = "R^2 vs Number of Predictors")

# Adjusted R^2
plot(adj_rsq, xlab = "Number of Predictors", ylab = "Adjusted R^2", type = "b", col = "green")
title(main = "Adjusted R^2 vs Number of Predictors")

# Cp 
plot(cp, xlab = "Number of Predictors", ylab = "Cp", type = "b", col = "red")
title(main = "Cp vs Number of Predictors")
abline(a = 0, b = 1, col = "blue", lty = 2)#p=cp之線

# BIC 
plot(bic, xlab = "Number of Predictors", ylab = "BIC(SBC)", type = "b", col = "orange")
title(main = "BIC(SBC) vs Number of Predictors")

# PRESS 
plot(press_p, xlab = "Number of Predictors", ylab = "PRESS", type = "b", col = "brown")
title(main = "PRESS vs Number of Predictors")

par(mfrow = c(1, 1))
dev.off()

#選定較佳的模型:p=4,5,10,11
#model_1_9
model_1_4 <-lm(log(y_train)  ~ Proc_chg +X1 + X3, data = x_train)
model_1_5 <-lm(log(y_train)  ~ Proc_chg +X1 + X2 + X5, data = x_train)
model_1_10 <-lm(log(y_train) ~ Backlog_G1+Backlog_G2+Backlog + Proc_chg +X1 + X2 + X5 + G1+ G2,, data = x_train)
model_1_11 <-lm(log(y_train) ~ Backlog_G1+Backlog_G2+Backlog+ Proc_chg +X1 + X2 +X4+ X5 + G1+ G2,, data = x_train)

#存下model_1_4之summary table
model_1_4_df <- as.data.frame(summary(model_1_4)$coefficients)
addWorksheet(wb, "model_1_4") 
writeData(wb, "model_1_4", model_1_4_df) 
saveWorkbook(wb, criterion_xlsx, overwrite = TRUE)

#存下model_1_5之summary table
model_1_5_df <- as.data.frame(summary(model_1_5)$coefficients)
addWorksheet(wb, "model_1_5") 
writeData(wb, "model_1_5", model_1_5_df) 
saveWorkbook(wb, criterion_xlsx, overwrite = TRUE)

#存下model_1_10之summary table
model_1_10_df <- as.data.frame(summary(model_1_10)$coefficients)
addWorksheet(wb, "model_1_10") 
writeData(wb, "model_1_10", model_1_10_df) 
saveWorkbook(wb, criterion_xlsx, overwrite = TRUE)

#存下model_1_11之summary table
model_1_11_df <- as.data.frame(summary(model_1_11)$coefficients)
addWorksheet(wb, "model_1_11") 
writeData(wb, "model_1_11", model_1_11_df) 
saveWorkbook(wb, criterion_xlsx, overwrite = TRUE)

#讀入測試資料集
data_split <- "C:/Users/User/OneDrive/Desktop/應用線性統計模型作業/final/data_split.xlsx"
data_test <- read_excel(data_split, sheet = 2)
y_test <- data_test[[c('Webs_num')]]
x_test <- data_test[,c('Backlog','Team_exp','Proc_chg','X1','X2','X3','X4','X5','G1','G2')]
y_test=y_test+1
x_test$'Backlog_Team_exp' <- x_test$Backlog * x_test$Team_exp
x_test$'Backlog_G1' <- x_test$Backlog * x_test$G1
x_test$'Backlog_G2' <- x_test$Backlog * x_test$G2

#用測試集資料fit模型
model_2_4 <-lm(log(y_test)  ~ Proc_chg +X1 + X3, data = x_test)
model_2_5 <-lm(log(y_test)  ~ Proc_chg +X1 + X2 + X5, data = x_test)
model_2_10 <-lm(log(y_test) ~ Backlog_G1+Backlog_G2+Backlog + Proc_chg +X1 + X2 + X5 + G1+ G2,, data = x_test)
model_2_11 <-lm(log(y_test) ~ Backlog_G1+Backlog_G2+Backlog+ Proc_chg +X1 + X2 +X4+ X5 + G1+ G2,, data = x_test)

#存下model_2_4之summary table
model_2_4_df <- as.data.frame(summary(model_2_4)$coefficients)
addWorksheet(wb, "model_2_4") 
writeData(wb, "model_2_4", model_2_4_df) 
saveWorkbook(wb, criterion_xlsx, overwrite = TRUE)

#存下model_2_5之summary table
model_2_5_df <- as.data.frame(summary(model_2_5)$coefficients)
addWorksheet(wb, "model_2_5") 
writeData(wb, "model_2_5", model_2_5_df) 
saveWorkbook(wb, criterion_xlsx, overwrite = TRUE)

#存下model_2_10之summary table
model_2_10_df <- as.data.frame(summary(model_2_10)$coefficients)
addWorksheet(wb, "model_2_10") 
writeData(wb, "model_2_10", model_2_10_df) 
saveWorkbook(wb, criterion_xlsx, overwrite = TRUE)

#存下model_2_11之summary table
model_2_11_df <- as.data.frame(summary(model_2_11)$coefficients)
addWorksheet(wb, "model_2_11") 
writeData(wb, "model_2_11", model_2_11_df) 
saveWorkbook(wb, criterion_xlsx, overwrite = TRUE)

#計算MSPR
y_pred_4=predict(model_1_4,x_test)
error=0
for (i in 1:length(y_test)){
  k=((y_test-y_pred_4)[i])^2
    error=error+k
}
MSPR_4 <- error/length(y_test)

y_pred_5=predict(model_1_5,x_test)
error=0
for (i in 1:length(y_test)){
  k=((y_test-y_pred_5)[i])^2
    error=error+k
}
MSPR_5 <- error/length(y_test)

y_pred_10=predict(model_1_10,x_test)
error=0
for (i in 1:length(y_test)){
  k=((y_test-y_pred_10)[i])^2
    error=error+k
}
MSPR_10 <- error/length(y_test)

y_pred_11=predict(model_1_11,x_test)
error=0
for (i in 1:length(y_test)){
  k=((y_test-y_pred_11)[i])^2
    error=error+k
}
MSPR_11 <- error/length(y_test)

#MSPR
MSPR=c(MSPR_4,MSPR_5,MSPR_10,MSPR_11)

#求MSE
SSE_4 <- sum(residuals(model_1_4)^2)
n <- nrow(x_train)
p <- length(coef(model_1_4))
df_residual <- n - p
MSE_4 <- SSE_4/df_residual

SSE_5 <- sum(residuals(model_1_5)^2)
n <- nrow(x_train)
p <- length(coef(model_1_5))
df_residual <- n - p
MSE_5 <- SSE_5/df_residual

SSE_10 <- sum(residuals(model_1_10)^2)
n <- nrow(x_train)
p <- length(coef(model_1_10))
df_residual <- n - p
MSE_10 <- SSE_10/df_residual

SSE_11 <- sum(residuals(model_1_11)^2)
n <- nrow(x_train)
p <- length(coef(model_1_11))
df_residual <- n - p
MSE_11 <- SSE_11/df_residual

MSE <- c(MSE_4,MSE_5,MSE_10,MSE_11)

#MSPR與MSE差距很大，沒有哪個比較好。通過比較係數，在p=4時有相對好的模型
#使用變數 Proc_chg +X1 + X3 fit所有的資料

final_model <- lm(log(y) ~  Proc_chg + X1 + X3 , data = webs.data)
summary(final_model)
anova(final_model)

#繪製最終結果的residual plot
residuals <- resid(final_model)
fitted_values <- fitted(final_model)
residual_plot_data <- data.frame(Fitted = fitted_values, Residuals = residuals)

ggplot(residual_plot_data, aes(x = Fitted, y = Residuals)) +
  geom_point() +
  geom_hline(yintercept = 0, linetype = "dashed", color = "red") +
  labs(title = "Residual Plot", x = "Fitted Values", y = "Residuals") +
  theme_minimal()

pdf(file = 'C:/Users/User/OneDrive/Desktop/應用線性統計模型作業/final/final_residual_plots.pdf')
ggplot(residual_plot_data, aes(x = Fitted, y = Residuals)) +
  geom_point() +
  geom_hline(yintercept = 0, linetype = "dashed", color = "red") +
  labs(title = "Residual Plot", x = "Fitted Values", y = "Residuals") +
  theme_minimal()
dev.off()




# 比較實際值與預測值
actual_values <- log(y)
predicted_values <- fitted(final_model)
comparison_data <- data.frame(Actual = actual_values, Predicted = predicted_values)

pdf(file = 'C:/Users/User/OneDrive/Desktop/應用線性統計模型作業/final/final_comparison_plots.pdf')
ggplot(comparison_data, aes(x = Actual, y = Predicted)) +
  geom_point() +
  geom_abline(slope = 1, intercept = 0, linetype = "dashed", color = "red") +
  labs(title = "Actual vs Predicted Values", x = "Actual Values", y = "Predicted Values") +
  theme_minimal()
dev.off()





